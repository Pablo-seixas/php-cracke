<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Exception\PagamentoInvalidoException;
use App\Exception\ReceitaObrigatoriaException;
use App\Service\CaixaService;
use App\Service\EstoqueService;
use App\Service\VendaService;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class VendaCaixaServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios', 'app.Categorias', 'app.Fabricantes', 'app.Medicamentos', 'app.Produtos',
        'app.Fornecedores', 'app.Lotes', 'app.MovimentacoesEstoque', 'app.Clientes',
        'app.Vendas', 'app.VendasItens', 'app.Caixas', 'app.Pagamentos',
        'app.MovimentacoesCaixa', 'app.Receitas', 'app.Auditorias',
    ];

    /** Finalize and cancel a product sale, restoring stock and payment. */
    public function testVendaCancelamentoEFechamentoDeCaixa(): void
    {
        [$usuario, $produto] = $this->referencias();
        $caixaService = new CaixaService();
        $caixa = $caixaService->abrir($usuario, 50);
        (new EstoqueService())->movimentar([
            'produto_id' => $produto->id, 'tipo' => 'entrada_manual',
            'quantidade' => 10, 'motivo' => 'Carga de teste',
        ], $usuario);
        $vendaService = new VendaService();
        $venda = $vendaService->finalizar([], [[
            'produto_id' => $produto->id, 'quantidade' => 3,
        ]], [[
            'forma' => 'pix', 'valor' => 12,
        ]], $usuario, $caixa);

        $this->assertSame('finalizada', $venda->status);
        $this->assertSame(12.0, (float)$venda->total);
        $vendaService->cancelar($venda, $usuario, 'Cancelamento de teste');
        $this->assertSame('cancelada', $venda->status);
        $pagamento = TableRegistry::getTableLocator()->get('Pagamentos')->find()->firstOrFail();
        $this->assertSame('estornado', $pagamento->status);
        $caixaService->fechar($caixa, $usuario);
        $this->assertSame(50.0, (float)$caixa->saldo_final);
        $this->assertSame(3, TableRegistry::getTableLocator()->get('MovimentacoesEstoque')->find()->count());
    }

    /** Reject payment totals that differ from the sale total. */
    public function testRejeitaPagamentoDivergente(): void
    {
        [$usuario, $produto] = $this->referencias();
        $caixa = (new CaixaService())->abrir($usuario, 0);
        (new EstoqueService())->movimentar([
            'produto_id' => $produto->id, 'tipo' => 'entrada_manual',
            'quantidade' => 5, 'motivo' => 'Carga de teste',
        ], $usuario);
        $this->expectException(PagamentoInvalidoException::class);
        (new VendaService())->finalizar([], [[
            'produto_id' => $produto->id, 'quantidade' => 1,
        ]], [[
            'forma' => 'dinheiro', 'valor' => 1,
        ]], $usuario, $caixa);
    }

    /** Require a prescription before selling restricted medicines. */
    public function testExigeReceitaParaMedicamentoRestrito(): void
    {
        [$usuario] = $this->referencias();
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $categoria = $categorias->find()->firstOrFail();
        $fabricantes = TableRegistry::getTableLocator()->get('Fabricantes');
        $fabricante = $fabricantes->newEntity(['nome' => 'Fabricante receita', 'ativo' => true]);
        $fabricantes->saveOrFail($fabricante);
        $medicamentos = TableRegistry::getTableLocator()->get('Medicamentos');
        $medicamento = $medicamentos->newEntity([
            'categoria_id' => $categoria->id, 'fabricante_id' => $fabricante->id,
            'codigo_interno' => 'REC-001', 'nome_comercial' => 'Medicamento restrito',
            'principio_ativo' => 'Teste', 'unidade_medida' => 'UN',
            'preco_custo' => 5, 'preco_venda' => 10, 'estoque_minimo' => 1,
            'estoque_maximo' => 20, 'necessita_receita' => true,
            'controlado' => false, 'refrigerado' => false, 'ativo' => true,
        ]);
        $medicamentos->saveOrFail($medicamento);
        $lotes = TableRegistry::getTableLocator()->get('Lotes');
        $lote = $lotes->newEntity([
            'medicamento_id' => $medicamento->id, 'numero_lote' => 'REC-LOTE-1',
            'data_validade' => Date::today()->addYears(1),
            'quantidade_recebida' => 5, 'quantidade_disponivel' => 5,
            'preco_custo' => 5, 'data_recebimento' => DateTime::now(),
            'usuario_id' => $usuario->id, 'bloqueado' => false, 'recolhido' => false,
        ]);
        $lotes->saveOrFail($lote);
        $caixa = (new CaixaService())->abrir($usuario, 0);

        $this->expectException(ReceitaObrigatoriaException::class);
        (new VendaService())->finalizar([], [[
            'medicamento_id' => $medicamento->id, 'quantidade' => 1,
        ]], [['forma' => 'pix', 'valor' => 10]], $usuario, $caixa);
    }

    /** @return array{0: \App\Model\Entity\Usuario, 1: \App\Model\Entity\Produto} */
    private function referencias(): array
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->newEntity([
            'nome' => 'Atendente Venda', 'email' => uniqid('venda-') . '@example.test',
            'senha' => 'ValidPassword!123', 'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $categoria = $categorias->newEntity(['nome' => uniqid('Venda '), 'ativo' => true]);
        $categorias->saveOrFail($categoria);
        $produtos = TableRegistry::getTableLocator()->get('Produtos');
        $produto = $produtos->newEntity([
            'categoria_id' => $categoria->id, 'codigo_interno' => uniqid('VENDA-'),
            'nome' => 'Produto venda', 'unidade_medida' => 'UN',
            'preco_custo' => 2, 'preco_venda' => 4, 'estoque_minimo' => 1,
            'estoque_maximo' => 20, 'ativo' => true,
        ]);
        $produtos->saveOrFail($produto);

        return [$usuario, $produto];
    }
}
