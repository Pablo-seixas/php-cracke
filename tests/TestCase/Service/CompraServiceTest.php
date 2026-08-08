<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Exception\CompraNaoAprovadaException;
use App\Service\CompraService;
use App\Service\FornecedorService;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class CompraServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios', 'app.Categorias', 'app.Fabricantes', 'app.Medicamentos', 'app.Produtos',
        'app.Fornecedores', 'app.SolicitacoesCompras', 'app.SolicitacoesComprasItens',
        'app.Cotacoes', 'app.CotacoesItens', 'app.Compras', 'app.ComprasItens', 'app.Auditorias',
        'app.Lotes', 'app.MovimentacoesEstoque', 'app.ContasPagar',
    ];

    /** Execute request, quotation, approval and order emission end-to-end. */
    public function testFluxoCompletoAteEmissaoDoPedido(): void
    {
        [$usuario, $produto, $fornecedor] = $this->referencias();
        $service = new CompraService();
        $solicitacao = $service->criarSolicitacao([
            'justificativa' => 'Reposição do estoque mínimo',
        ], [[
            'produto_id' => $produto->id, 'quantidade' => 10, 'preco_estimado' => 3,
        ]], $usuario);
        $service->enviar($solicitacao, $usuario);
        $item = TableRegistry::getTableLocator()->get('SolicitacoesComprasItens')->find()->firstOrFail();
        $cotacao = $service->cotar($solicitacao, $fornecedor->id, [
            'prazo_entrega' => 5, 'condicoes_pagamento' => '30 dias',
        ], [[
            'solicitacao_compra_item_id' => $item->id, 'quantidade' => 10, 'preco_unitario' => 2.5,
        ]], $usuario);
        $service->selecionarCotacao($solicitacao, $cotacao, $usuario);
        $service->decidir($solicitacao, $usuario, true);
        $compra = $service->emitirPedido($solicitacao, $usuario);
        $itemCompra = TableRegistry::getTableLocator()->get('ComprasItens')->find()->firstOrFail();
        $service->receberPedido($compra, [[
            'compra_item_id' => $itemCompra->id, 'quantidade' => 10,
        ]], $usuario);

        $this->assertSame('recebida', $compra->status);
        $this->assertSame(25.0, (float)$compra->valor_total);
        $this->assertSame(1, TableRegistry::getTableLocator()->get('ComprasItens')->find()->count());
        $this->assertSame(1, TableRegistry::getTableLocator()->get('MovimentacoesEstoque')->find()->count());
        $this->assertSame(1, TableRegistry::getTableLocator()->get('ContasPagar')->find()->count());
    }

    /** Prevent order emission before approval. */
    public function testBloqueiaPedidoNaoAprovado(): void
    {
        [$usuario, $produto] = $this->referencias();
        $solicitacao = (new CompraService())->criarSolicitacao([
            'justificativa' => 'Teste de bloqueio',
        ], [[
            'produto_id' => $produto->id, 'quantidade' => 1, 'preco_estimado' => 3,
        ]], $usuario);

        $this->expectException(CompraNaoAprovadaException::class);
        (new CompraService())->emitirPedido($solicitacao, $usuario);
    }

    /** Reject invalid supplier documents at the domain boundary. */
    public function testRejeitaCnpjInvalido(): void
    {
        [$usuario] = $this->referencias(false);
        $fornecedores = TableRegistry::getTableLocator()->get('Fornecedores');
        $this->expectException(PersistenceFailedException::class);
        (new FornecedorService())->salvar($fornecedores->newEmptyEntity(), [
            'razao_social' => 'Fornecedor Inválido', 'cnpj' => '11111111111111',
            'prazo_medio_entrega' => 0, 'ativo' => true,
        ], $usuario);
    }

    /** @return array{0: object, 1: object, 2?: object} */
    private function referencias(bool $criarFornecedor = true): array
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->newEntity([
            'nome' => 'Comprador Teste', 'email' => uniqid('comprador-') . '@example.test',
            'senha' => 'ValidPassword!123', 'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        $categorias = TableRegistry::getTableLocator()->get('Categorias');
        $categoria = $categorias->newEntity(['nome' => uniqid('Compras '), 'ativo' => true]);
        $categorias->saveOrFail($categoria);
        $produtos = TableRegistry::getTableLocator()->get('Produtos');
        $produto = $produtos->newEntity([
            'categoria_id' => $categoria->id, 'codigo_interno' => uniqid('COMP-'),
            'nome' => 'Produto para compra', 'unidade_medida' => 'UN',
            'preco_custo' => 2, 'preco_venda' => 4, 'estoque_minimo' => 2,
            'estoque_maximo' => 20, 'ativo' => true,
        ]);
        $produtos->saveOrFail($produto);
        if (!$criarFornecedor) {
            return [$usuario, $produto];
        }
        $fornecedores = TableRegistry::getTableLocator()->get('Fornecedores');
        $fornecedor = (new FornecedorService())->salvar($fornecedores->newEmptyEntity(), [
            'razao_social' => 'Fornecedor Válido ' . uniqid(), 'cnpj' => '11222333000181',
            'prazo_medio_entrega' => 5, 'ativo' => true,
        ], $usuario);

        return [$usuario, $produto, $fornecedor];
    }
}
