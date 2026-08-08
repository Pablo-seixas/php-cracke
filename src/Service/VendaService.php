<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\EstoqueInsuficienteException;
use App\Exception\MedicamentoInativoException;
use App\Exception\PagamentoInvalidoException;
use App\Exception\ReceitaObrigatoriaException;
use App\Model\Entity\Caixa;
use App\Model\Entity\Usuario;
use App\Model\Entity\Venda;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use DomainException;

final class VendaService
{
    private const FORMAS_PAGAMENTO = ['dinheiro', 'pix', 'debito', 'credito', 'convenio'];

    /** Finalize a sale, stock outputs and payments in one transaction. */
    public function finalizar(
        array $dados,
        array $itens,
        array $pagamentos,
        Usuario $usuario,
        Caixa $caixa,
    ): Venda {
        if ($caixa->status !== 'aberto') {
            throw new PagamentoInvalidoException('Abra um caixa antes de finalizar a venda.');
        }
        if ($itens === []) {
            throw new DomainException('A venda deve possuir ao menos um item.');
        }

        return ConnectionManager::get('default')->transactional(function () use (
            $dados,
            $itens,
            $pagamentos,
            $usuario,
            $caixa,
        ): Venda {
            $vendas = TableRegistry::getTableLocator()->get('Vendas');
            $venda = $vendas->newEntity([
                'numero' => $this->numero(), 'cliente_id' => $dados['cliente_id'] ?? null ?: null,
                'usuario_id' => $usuario->id, 'status' => 'aberta',
                'subtotal' => 0, 'desconto' => max(0, (float)($dados['desconto'] ?? 0)),
                'acrescimo' => max(0, (float)($dados['acrescimo'] ?? 0)), 'total' => 0,
            ]);
            $vendas->saveOrFail($venda);
            $subtotal = 0.0;
            foreach ($itens as $item) {
                $subtotal += $this->processarItem($venda, $item, $usuario);
            }
            $total = max(0, $subtotal - (float)$venda->desconto + (float)$venda->acrescimo);
            $this->registrarPagamentos($venda, $pagamentos, $total, $usuario, $caixa);
            $venda->subtotal = $subtotal;
            $venda->total = $total;
            $venda->status = 'finalizada';
            $venda->finalizada_em = DateTime::now();
            $vendas->saveOrFail($venda);
            $this->auditar($usuario, 'venda', $venda);

            return $venda;
        });
    }

    /** Cancel a finalized sale, restore stock and reverse all payments. */
    public function cancelar(Venda $venda, Usuario $usuario, string $motivo): void
    {
        if ($venda->status !== 'finalizada' || trim($motivo) === '') {
            throw new DomainException('Venda não pode ser cancelada ou o motivo não foi informado.');
        }
        ConnectionManager::get('default')->transactional(function () use ($venda, $usuario, $motivo): void {
            $itens = TableRegistry::getTableLocator()->get('VendasItens')->find()
                ->where(['venda_id' => $venda->id])->all();
            $estoque = new EstoqueService();
            foreach ($itens as $item) {
                $movimento = [
                    'tipo' => 'cancelamento', 'quantidade' => $item->quantidade,
                    'motivo' => 'Cancelamento da venda ' . $venda->numero,
                    'documento_tipo' => 'venda', 'documento_id' => $venda->id,
                ];
                if ($item->medicamento_id !== null) {
                    $movimento += ['medicamento_id' => $item->medicamento_id, 'lote_id' => $item->lote_id];
                } else {
                    $movimento['produto_id'] = $item->produto_id;
                }
                $estoque->movimentar($movimento, $usuario);
            }
            $pagamentos = TableRegistry::getTableLocator()->get('Pagamentos')->find()
                ->where(['venda_id' => $venda->id, 'status' => 'pago'])->all();
            foreach ($pagamentos as $pagamento) {
                $pagamento->status = 'estornado';
                $pagamento->estornado_em = DateTime::now();
                TableRegistry::getTableLocator()->get('Pagamentos')->saveOrFail($pagamento);
                $this->movimentarCaixa(
                    (int)$pagamento->caixa_id,
                    $usuario,
                    'saida',
                    (float)$pagamento->valor,
                    'Estorno da venda ' . $venda->numero,
                    (int)$pagamento->id,
                );
            }
            $venda->status = 'cancelada';
            $venda->cancelada_em = DateTime::now();
            $venda->motivo_cancelamento = $motivo;
            TableRegistry::getTableLocator()->get('Vendas')->saveOrFail($venda);
            $this->auditar($usuario, 'cancelamento_venda', $venda);
        });
    }

    /** Process a product or medication, splitting medication quantities by FEFO. */
    private function processarItem(Venda $venda, array $item, Usuario $usuario): float
    {
        $quantidade = (float)($item['quantidade'] ?? 0);
        if ($quantidade <= 0 || (empty($item['medicamento_id']) === empty($item['produto_id']))) {
            throw new DomainException('Item de venda inválido.');
        }
        if (!empty($item['medicamento_id'])) {
            return $this->processarMedicamento($venda, $item, $quantidade, $usuario);
        }

        $produto = TableRegistry::getTableLocator()->get('Produtos')->get($item['produto_id']);
        if (!$produto->ativo) {
            throw new MedicamentoInativoException('Produto inativo não pode ser vendido.');
        }
        $preco = (float)($item['preco_unitario'] ?? $produto->preco_venda);
        $total = max(0, $quantidade * $preco - (float)($item['desconto'] ?? 0));
        (new EstoqueService())->movimentar([
            'produto_id' => $produto->id, 'tipo' => 'saida_venda', 'quantidade' => $quantidade,
            'motivo' => 'Venda ' . $venda->numero, 'documento_tipo' => 'venda', 'documento_id' => $venda->id,
        ], $usuario);
        $this->salvarItem($venda, $item + ['produto_id' => $produto->id], $quantidade, $preco, $total);

        return $total;
    }

    /** Select valid medication lots in FEFO order. */
    private function processarMedicamento(Venda $venda, array $item, float $quantidade, Usuario $usuario): float
    {
        $medicamento = TableRegistry::getTableLocator()->get('Medicamentos')->get($item['medicamento_id']);
        if (!$medicamento->ativo) {
            throw new MedicamentoInativoException('Medicamento inativo não pode ser vendido.');
        }
        if (($medicamento->necessita_receita || $medicamento->controlado) && empty($item['receita_id'])) {
            throw new ReceitaObrigatoriaException('Registre a receita antes de vender este medicamento.');
        }
        $lotes = TableRegistry::getTableLocator()->get('Lotes')->find()
            ->where([
                'medicamento_id' => $medicamento->id, 'quantidade_disponivel >' => 0,
                'bloqueado' => false, 'recolhido' => false, 'data_validade >=' => Date::today(),
            ])->orderByAsc('data_validade')->epilog('FOR UPDATE')->all();
        $restante = $quantidade;
        $preco = (float)($item['preco_unitario'] ?? $medicamento->preco_venda);
        $totalVenda = 0.0;
        foreach ($lotes as $lote) {
            if ($restante <= 0) {
                break;
            }
            $usar = min($restante, (float)$lote->quantidade_disponivel);
            (new EstoqueService())->movimentar([
                'medicamento_id' => $medicamento->id, 'lote_id' => $lote->id,
                'tipo' => 'saida_venda', 'quantidade' => $usar,
                'motivo' => 'Venda ' . $venda->numero, 'documento_tipo' => 'venda', 'documento_id' => $venda->id,
            ], $usuario);
            $desconto = $restante === $quantidade ? (float)($item['desconto'] ?? 0) : 0.0;
            $total = max(0, $usar * $preco - $desconto);
            $this->salvarItem($venda, $item + ['lote_id' => $lote->id], $usar, $preco, $total);
            $totalVenda += $total;
            $restante -= $usar;
        }
        if ($restante > 0) {
            throw new EstoqueInsuficienteException('Estoque FEFO insuficiente para o medicamento.');
        }

        return $totalVenda;
    }

    /** Persist one immutable sale item. */
    private function salvarItem(Venda $venda, array $item, float $quantidade, float $preco, float $total): void
    {
        $itens = TableRegistry::getTableLocator()->get('VendasItens');
        $itens->saveOrFail($itens->newEntity([
            'venda_id' => $venda->id, 'medicamento_id' => $item['medicamento_id'] ?? null,
            'produto_id' => $item['produto_id'] ?? null, 'lote_id' => $item['lote_id'] ?? null,
            'receita_id' => $item['receita_id'] ?? null, 'quantidade' => $quantidade,
            'preco_unitario' => $preco, 'desconto' => $item['desconto'] ?? 0, 'total' => $total,
        ]));
    }

    /** Validate mixed payments and reflect each one in the cash ledger. */
    private function registrarPagamentos(
        Venda $venda,
        array $dadosPagamentos,
        float $total,
        Usuario $usuario,
        Caixa $caixa,
    ): void {
        $soma = array_sum(array_map(fn(array $pagamento): float => (float)$pagamento['valor'], $dadosPagamentos));
        if ($dadosPagamentos === [] || abs($soma - $total) > 0.009) {
            throw new PagamentoInvalidoException('A soma dos pagamentos deve ser igual ao total da venda.');
        }
        $pagamentos = TableRegistry::getTableLocator()->get('Pagamentos');
        foreach ($dadosPagamentos as $dados) {
            if (!in_array($dados['forma'], self::FORMAS_PAGAMENTO, true) || (float)$dados['valor'] <= 0) {
                throw new PagamentoInvalidoException('Forma ou valor de pagamento inválido.');
            }
            $pagamento = $pagamentos->newEntity([
                'venda_id' => $venda->id, 'caixa_id' => $caixa->id,
                'forma' => $dados['forma'], 'valor' => $dados['valor'],
                'status' => 'pago', 'pago_em' => DateTime::now(), 'referencia' => $dados['referencia'] ?? null,
            ]);
            $pagamentos->saveOrFail($pagamento);
            $this->movimentarCaixa($caixa->id, $usuario, 'entrada', (float)$pagamento->valor, 'Venda ' . $venda->numero, $pagamento->id);
        }
    }

    /** Add a payment-linked immutable cash movement. */
    private function movimentarCaixa(
        int $caixaId,
        Usuario $usuario,
        string $tipo,
        float $valor,
        string $motivo,
        int $pagamentoId,
    ): void {
        $movimentos = TableRegistry::getTableLocator()->get('MovimentacoesCaixa');
        $movimentos->saveOrFail($movimentos->newEntity([
            'caixa_id' => $caixaId, 'pagamento_id' => $pagamentoId, 'usuario_id' => $usuario->id,
            'tipo' => $tipo, 'valor' => $valor, 'motivo' => $motivo,
        ]));
    }

    /** Generate a unique sale number. */
    private function numero(): string
    {
        return 'VD-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /** Persist a sale audit event. */
    private function auditar(Usuario $usuario, string $acao, Venda $venda): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => 'vendas',
            'registro_id' => $venda->id, 'dados_posteriores' => $venda->toArray(),
        ]));
    }
}
