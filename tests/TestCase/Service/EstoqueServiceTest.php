<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Exception\EstoqueInsuficienteException;
use App\Exception\LoteBloqueadoException;
use App\Exception\MedicamentoVencidoException;
use App\Service\EstoqueService;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class EstoqueServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios', 'app.Categorias', 'app.Fabricantes', 'app.Medicamentos',
        'app.Produtos', 'app.Fornecedores', 'app.Lotes', 'app.MovimentacoesEstoque',
        'app.Auditorias',
    ];

    /** Register a lot output and preserve previous and posterior balances. */
    public function testSaidaDeLoteAtualizaSaldo(): void
    {
        [$usuario, $medicamento, $lote] = $this->cenarioMedicamento();
        $movimento = (new EstoqueService())->movimentar([
            'medicamento_id' => $medicamento->id,
            'lote_id' => $lote->id,
            'tipo' => 'saida_manual',
            'quantidade' => 4,
            'motivo' => 'Consumo de teste',
        ], $usuario);

        $this->assertSame(10.0, (float)$movimento->quantidade_anterior);
        $this->assertSame(6.0, (float)$movimento->quantidade_posterior);
        $this->assertSame(6.0, (float)TableRegistry::getTableLocator()->get('Lotes')->get($lote->id)->quantidade_disponivel);
    }

    /** Reject output larger than lot availability. */
    public function testRejeitaEstoqueInsuficiente(): void
    {
        [$usuario, $medicamento, $lote] = $this->cenarioMedicamento();
        $this->expectException(EstoqueInsuficienteException::class);
        (new EstoqueService())->movimentar([
            'medicamento_id' => $medicamento->id, 'lote_id' => $lote->id,
            'tipo' => 'saida_manual', 'quantidade' => 11, 'motivo' => 'Teste',
        ], $usuario);
    }

    /** Reject blocked lots on output. */
    public function testRejeitaLoteBloqueado(): void
    {
        [$usuario, $medicamento, $lote] = $this->cenarioMedicamento();
        $lote->bloqueado = true;
        TableRegistry::getTableLocator()->get('Lotes')->saveOrFail($lote);
        $this->expectException(LoteBloqueadoException::class);
        (new EstoqueService())->movimentar([
            'medicamento_id' => $medicamento->id, 'lote_id' => $lote->id,
            'tipo' => 'saida_manual', 'quantidade' => 1, 'motivo' => 'Teste',
        ], $usuario);
    }

    /** Reject expired lots on output. */
    public function testRejeitaLoteVencido(): void
    {
        [$usuario, $medicamento, $lote] = $this->cenarioMedicamento();
        $lote->data_validade = Date::yesterday();
        TableRegistry::getTableLocator()->get('Lotes')->saveOrFail($lote);
        $this->expectException(MedicamentoVencidoException::class);
        (new EstoqueService())->movimentar([
            'medicamento_id' => $medicamento->id, 'lote_id' => $lote->id,
            'tipo' => 'saida_manual', 'quantidade' => 1, 'motivo' => 'Teste',
        ], $usuario);
    }

    /** Derive product balance from its immutable movement ledger. */
    public function testEntradaESaidaDeProduto(): void
    {
        [$usuario, $categoria] = $this->referencias();
        $produtos = TableRegistry::getTableLocator()->get('Produtos');
        $produto = $produtos->newEntity([
            'categoria_id' => $categoria->id, 'codigo_interno' => 'EST-PROD-001',
            'nome' => 'Produto de estoque', 'unidade_medida' => 'UN',
            'preco_custo' => 2, 'preco_venda' => 4, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'ativo' => true,
        ]);
        $produtos->saveOrFail($produto);
        $service = new EstoqueService();
        $service->movimentar([
            'produto_id' => $produto->id, 'tipo' => 'entrada_manual',
            'quantidade' => 10, 'motivo' => 'Carga inicial',
        ], $usuario);
        $saida = $service->movimentar([
            'produto_id' => $produto->id, 'tipo' => 'saida_manual',
            'quantidade' => 3, 'motivo' => 'Consumo',
        ], $usuario);

        $this->assertSame(10.0, (float)$saida->quantidade_anterior);
        $this->assertSame(7.0, (float)$saida->quantidade_posterior);
    }

    /** Inventory replaces the calculated balance instead of adding to it. */
    public function testInventarioRedefineSaldoDoProduto(): void
    {
        [$usuario, $categoria] = $this->referencias();
        $produtos = TableRegistry::getTableLocator()->get('Produtos');
        $produto = $produtos->newEntity([
            'categoria_id' => $categoria->id, 'codigo_interno' => 'INV-PROD-001',
            'nome' => 'Produto inventário', 'unidade_medida' => 'UN',
            'preco_custo' => 2, 'preco_venda' => 4, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'ativo' => true,
        ]);
        $produtos->saveOrFail($produto);
        $service = new EstoqueService();
        $service->movimentar(['produto_id' => $produto->id, 'tipo' => 'entrada_manual', 'quantidade' => 10, 'motivo' => 'Entrada'], $usuario);
        $inventario = $service->movimentar(['produto_id' => $produto->id, 'tipo' => 'inventario', 'quantidade' => 4, 'motivo' => 'Contagem'], $usuario);
        $saida = $service->movimentar(['produto_id' => $produto->id, 'tipo' => 'saida_manual', 'quantidade' => 1, 'motivo' => 'Saída'], $usuario);

        $this->assertSame(4.0, (float)$inventario->quantidade_posterior);
        $this->assertSame(4.0, (float)$saida->quantidade_anterior);
        $this->assertSame(3.0, (float)$saida->quantidade_posterior);
    }

    /** @return array{0: \App\Model\Entity\Usuario, 1: object, 2: object} */
    private function cenarioMedicamento(): array
    {
        [$usuario, $categoria, $fabricante] = $this->referencias();
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos');
        $medicamento = $medicamentos->newEntity([
            'categoria_id' => $categoria->id, 'fabricante_id' => $fabricante->id,
            'codigo_interno' => uniqid('EST-MED-'), 'nome_comercial' => 'Medicamento Estoque',
            'principio_ativo' => 'Substância teste', 'unidade_medida' => 'UN',
            'preco_custo' => 2, 'preco_venda' => 4, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'necessita_receita' => false, 'controlado' => false,
            'refrigerado' => false, 'ativo' => true,
        ]);
        $medicamentos->saveOrFail($medicamento);
        $lotes = TableRegistry::getTableLocator()->get('Lotes');
        $lote = $lotes->newEntity([
            'medicamento_id' => $medicamento->id, 'numero_lote' => uniqid('LOTE-'),
            'data_validade' => Date::today()->addYears(1), 'quantidade_recebida' => 10,
            'quantidade_disponivel' => 10, 'preco_custo' => 2,
            'data_recebimento' => DateTime::now(), 'usuario_id' => $usuario->id,
            'bloqueado' => false, 'recolhido' => false,
        ]);
        $lotes->saveOrFail($lote);

        return [$usuario, $medicamento, $lote];
    }

    /** @return array{0: \App\Model\Entity\Usuario, 1: object, 2: object} */
    private function referencias(): array
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->newEntity([
            'nome' => 'Estoquista Teste', 'email' => uniqid('estoque-') . '@example.test',
            'senha' => 'ValidPassword!123', 'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $categoria = $categorias->newEntity(['nome' => uniqid('Categoria '), 'ativo' => true]);
        $categorias->saveOrFail($categoria);
        $fabricantes = TableRegistry::getTableLocator()->get('Fabricantes');
        $fabricante = $fabricantes->newEntity(['nome' => uniqid('Fabricante '), 'ativo' => true]);
        $fabricantes->saveOrFail($fabricante);

        return [$usuario, $categoria, $fabricante];
    }
}
