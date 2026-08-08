<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\CompraNaoAprovadaException;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use DomainException;

final class CompraService
{
    /** Create a draft purchase request and its items atomically. */
    public function criarSolicitacao(array $dados, array $itens, Usuario $solicitante): EntityInterface
    {
        if ($itens === []) {
            throw new DomainException('A solicitação deve possuir ao menos um item.');
        }

        return ConnectionManager::get('default')->transactional(function () use (
            $dados,
            $itens,
            $solicitante,
        ): EntityInterface {
            $solicitacoes = TableRegistry::getTableLocator()->get('SolicitacoesCompras');
            $dados += [
                'numero' => $this->numero('SC'),
                'solicitante_id' => $solicitante->id,
                'status' => 'rascunho',
                'valor_estimado' => $this->totalItens($itens, 'preco_estimado'),
            ];
            $solicitacao = $solicitacoes->newEntity($dados);
            $solicitacoes->saveOrFail($solicitacao);
            $tableItens = TableRegistry::getTableLocator()->get('SolicitacoesComprasItens');
            foreach ($itens as $dadosItem) {
                $this->validarItem($dadosItem);
                $item = $tableItens->newEntity($dadosItem + ['solicitacao_compra_id' => $solicitacao->id]);
                $tableItens->saveOrFail($item);
            }
            $this->auditar($solicitante, 'criacao', 'solicitacoes_compras', $solicitacao);

            return $solicitacao;
        });
    }

    /** Send a draft request to the quotation phase. */
    public function enviar(EntityInterface $solicitacao, Usuario $usuario): EntityInterface
    {
        $this->exigirStatus($solicitacao, ['rascunho']);
        $solicitacao->set('status', 'solicitada');
        $solicitacao->set('solicitada_em', DateTime::now());
        TableRegistry::getTableLocator()->get('SolicitacoesCompras')->saveOrFail($solicitacao);
        $this->auditar($usuario, 'envio_cotacao', 'solicitacoes_compras', $solicitacao);

        return $solicitacao;
    }

    /** Register one supplier quotation and its item prices. */
    public function cotar(
        EntityInterface $solicitacao,
        int $fornecedorId,
        array $dados,
        array $itens,
        Usuario $usuario,
    ): EntityInterface {
        $this->exigirStatus($solicitacao, ['solicitada', 'em_cotacao']);
        if ($itens === []) {
            throw new DomainException('A cotação deve possuir itens.');
        }

        return ConnectionManager::get('default')->transactional(function () use (
            $solicitacao,
            $fornecedorId,
            $dados,
            $itens,
            $usuario,
        ): EntityInterface {
            $cotacoes = TableRegistry::getTableLocator()->get('Cotacoes');
            $cotacao = $cotacoes->newEntity($dados + [
                'solicitacao_compra_id' => $solicitacao->get('id'),
                'fornecedor_id' => $fornecedorId,
                'valor_total' => $this->totalItens($itens, 'preco_unitario'),
                'status' => 'aberta',
            ]);
            $cotacoes->saveOrFail($cotacao);
            $tableItens = TableRegistry::getTableLocator()->get('CotacoesItens');
            foreach ($itens as $dadosItem) {
                $itemSolicitado = TableRegistry::getTableLocator()->get('SolicitacoesComprasItens')
                    ->get($dadosItem['solicitacao_compra_item_id']);
                if ((int)$itemSolicitado->solicitacao_compra_id !== (int)$solicitacao->get('id')) {
                    throw new DomainException('Item de cotação não pertence à solicitação.');
                }
                $tableItens->saveOrFail($tableItens->newEntity($dadosItem + ['cotacao_id' => $cotacao->id]));
            }
            $solicitacao->set('status', 'em_cotacao');
            TableRegistry::getTableLocator()->get('SolicitacoesCompras')->saveOrFail($solicitacao);
            $this->auditar($usuario, 'cotacao', 'cotacoes', $cotacao);

            return $cotacao;
        });
    }

    /** Select the winning quotation and submit the request for approval. */
    public function selecionarCotacao(EntityInterface $solicitacao, EntityInterface $cotacao, Usuario $usuario): void
    {
        $this->exigirStatus($solicitacao, ['em_cotacao']);
        if ((int)$cotacao->get('solicitacao_compra_id') !== (int)$solicitacao->get('id')) {
            throw new DomainException('Cotação não pertence à solicitação.');
        }
        $solicitacao->set('fornecedor_escolhido_id', $cotacao->get('fornecedor_id'));
        $solicitacao->set('valor_aprovado', $cotacao->get('valor_total'));
        $solicitacao->set('status', 'aguardando_aprovacao');
        $cotacao->set('status', 'selecionada');
        TableRegistry::getTableLocator()->get('SolicitacoesCompras')->saveOrFail($solicitacao);
        TableRegistry::getTableLocator()->get('Cotacoes')->saveOrFail($cotacao);
        $this->auditar($usuario, 'selecao_cotacao', 'solicitacoes_compras', $solicitacao);
    }

    /** Approve or reject a request awaiting approval. */
    public function decidir(EntityInterface $solicitacao, Usuario $aprovador, bool $aprovar, string $motivo = ''): void
    {
        $this->exigirStatus($solicitacao, ['aguardando_aprovacao']);
        if (!$aprovar && trim($motivo) === '') {
            throw new DomainException('Informe o motivo da rejeição.');
        }
        $solicitacao->set('status', $aprovar ? 'aprovada' : 'rejeitada');
        $solicitacao->set('aprovador_id', $aprovador->id);
        $solicitacao->set('aprovada_em', DateTime::now());
        if (!$aprovar) {
            $solicitacao->set('justificativa', $solicitacao->get('justificativa') . "\nRejeição: {$motivo}");
        }
        TableRegistry::getTableLocator()->get('SolicitacoesCompras')->saveOrFail($solicitacao);
        $this->auditar(
            $aprovador,
            $aprovar ? 'aprovacao_compra' : 'rejeicao_compra',
            'solicitacoes_compras',
            $solicitacao,
        );
    }

    /** Emit a purchase order from the selected quotation of an approved request. */
    public function emitirPedido(EntityInterface $solicitacao, Usuario $usuario): EntityInterface
    {
        if ($solicitacao->get('status') !== 'aprovada') {
            throw new CompraNaoAprovadaException('Somente solicitações aprovadas podem gerar pedidos.');
        }

        return ConnectionManager::get('default')->transactional(function () use ($solicitacao, $usuario): EntityInterface {
            $cotacao = TableRegistry::getTableLocator()->get('Cotacoes')->find()
                ->contain(['CotacoesItens'])
                ->where(['solicitacao_compra_id' => $solicitacao->get('id'), 'status' => 'selecionada'])
                ->firstOrFail();
            $compras = TableRegistry::getTableLocator()->get('Compras');
            $compra = $compras->newEntity([
                'solicitacao_compra_id' => $solicitacao->get('id'),
                'fornecedor_id' => $cotacao->fornecedor_id,
                'numero' => $this->numero('PC'),
                'status' => 'emitida',
                'valor_total' => $cotacao->valor_total,
                'emitida_em' => DateTime::now(),
                'usuario_id' => $usuario->id,
            ]);
            $compras->saveOrFail($compra);
            $itensCompra = TableRegistry::getTableLocator()->get('ComprasItens');
            foreach ($cotacao->cotacoes_itens as $itemCotacao) {
                $solicitado = TableRegistry::getTableLocator()->get('SolicitacoesComprasItens')
                    ->get($itemCotacao->solicitacao_compra_item_id);
                $itensCompra->saveOrFail($itensCompra->newEntity([
                    'compra_id' => $compra->id,
                    'medicamento_id' => $solicitado->medicamento_id,
                    'produto_id' => $solicitado->produto_id,
                    'quantidade' => $itemCotacao->quantidade,
                    'quantidade_recebida' => 0,
                    'preco_unitario' => $itemCotacao->preco_unitario,
                ]));
            }
            $solicitacao->set('status', 'pedido_enviado');
            TableRegistry::getTableLocator()->get('SolicitacoesCompras')->saveOrFail($solicitacao);
            $this->auditar($usuario, 'emissao_pedido', 'compras', $compra);

            return $compra;
        });
    }

    /** Receive purchase items, update stock and create the payable account. */
    public function receberPedido(
        EntityInterface $compra,
        array $recebimentos,
        Usuario $usuario,
        ?Date $vencimento = null,
    ): EntityInterface {
        if (!in_array($compra->get('status'), ['emitida', 'recebida_parcialmente'], true)) {
            throw new DomainException('O pedido não está disponível para recebimento.');
        }
        if ($recebimentos === []) {
            throw new DomainException('Informe ao menos um item recebido.');
        }

        return ConnectionManager::get('default')->transactional(function () use (
            $compra,
            $recebimentos,
            $usuario,
            $vencimento,
        ): EntityInterface {
            $itensCompra = TableRegistry::getTableLocator()->get('ComprasItens');
            $estoque = new EstoqueService();
            foreach ($recebimentos as $recebimento) {
                $item = $itensCompra->get($recebimento['compra_item_id']);
                if ((int)$item->compra_id !== (int)$compra->get('id')) {
                    throw new DomainException('Item recebido não pertence ao pedido.');
                }
                $quantidade = (float)($recebimento['quantidade'] ?? 0);
                $novoTotal = (float)$item->quantidade_recebida + $quantidade;
                if ($quantidade <= 0 || $novoTotal > (float)$item->quantidade) {
                    throw new DomainException('Quantidade recebida inválida.');
                }
                $movimento = [
                    'tipo' => 'entrada_compra', 'quantidade' => $quantidade,
                    'motivo' => 'Recebimento do pedido ' . $compra->get('numero'),
                    'documento_tipo' => 'compra', 'documento_id' => $compra->get('id'),
                ];
                if ($item->medicamento_id !== null) {
                    $lotes = TableRegistry::getTableLocator()->get('Lotes');
                    $lote = $lotes->newEntity([
                        'medicamento_id' => $item->medicamento_id,
                        'fornecedor_id' => $compra->get('fornecedor_id'),
                        'numero_lote' => $recebimento['numero_lote'] ?? '',
                        'data_fabricacao' => $recebimento['data_fabricacao'] ?? null,
                        'data_validade' => $recebimento['data_validade'] ?? null,
                        'quantidade_recebida' => $quantidade, 'quantidade_disponivel' => 0,
                        'preco_custo' => $item->preco_unitario,
                        'nota_fiscal' => $compra->get('nota_fiscal'),
                        'data_recebimento' => DateTime::now(), 'usuario_id' => $usuario->id,
                        'bloqueado' => false, 'recolhido' => false,
                    ]);
                    $lotes->saveOrFail($lote);
                    $movimento += ['medicamento_id' => $item->medicamento_id, 'lote_id' => $lote->id];
                } else {
                    $movimento['produto_id'] = $item->produto_id;
                }
                $estoque->movimentar($movimento, $usuario);
                $item->quantidade_recebida = $novoTotal;
                $itensCompra->saveOrFail($item);
            }

            $pendentes = $itensCompra->find()->where([
                'compra_id' => $compra->get('id'),
                'quantidade_recebida < quantidade',
            ])->count();
            $completo = $pendentes === 0;
            $compra->set('status', $completo ? 'recebida' : 'recebida_parcialmente');
            $compra->set('recebida_em', $completo ? DateTime::now() : null);
            TableRegistry::getTableLocator()->get('Compras')->saveOrFail($compra);
            $solicitacao = TableRegistry::getTableLocator()->get('SolicitacoesCompras')
                ->get($compra->get('solicitacao_compra_id'));
            $solicitacao->set('status', $completo ? 'recebida' : 'recebida_parcialmente');
            TableRegistry::getTableLocator()->get('SolicitacoesCompras')->saveOrFail($solicitacao);
            if ($completo) {
                $contas = TableRegistry::getTableLocator()->get('ContasPagar');
                if (!$contas->exists(['compra_id' => $compra->get('id')])) {
                    $contas->saveOrFail($contas->newEntity([
                        'compra_id' => $compra->get('id'),
                        'fornecedor_id' => $compra->get('fornecedor_id'),
                        'descricao' => 'Pedido ' . $compra->get('numero'),
                        'valor' => $compra->get('valor_total'),
                        'vencimento' => $vencimento ?? Date::today()->addDays(30),
                        'parcela' => 1, 'total_parcelas' => 1,
                        'juros' => 0, 'multa' => 0, 'desconto' => 0, 'status' => 'aberta',
                    ]));
                }
            }
            $this->auditar($usuario, 'recebimento_compra', 'compras', $compra);

            return $compra;
        });
    }

    /** Validate that each request item references exactly one catalog item. */
    private function validarItem(array $item): void
    {
        if (empty($item['medicamento_id']) === empty($item['produto_id'])) {
            throw new DomainException('Cada item deve informar medicamento ou produto, exclusivamente.');
        }
        if ((float)($item['quantidade'] ?? 0) <= 0) {
            throw new DomainException('A quantidade solicitada deve ser positiva.');
        }
    }

    /** Calculate an item collection total. */
    private function totalItens(array $itens, string $campoPreco): float
    {
        return array_reduce(
            $itens,
            fn(float $total, array $item): float => $total + (float)$item['quantidade'] * (float)($item[$campoPreco] ?? 0),
            0.0,
        );
    }

    /** Reject invalid state transitions. */
    private function exigirStatus(EntityInterface $entidade, array $permitidos): void
    {
        if (!in_array($entidade->get('status'), $permitidos, true)) {
            throw new DomainException('Transição de status inválida para esta operação.');
        }
    }

    /** Generate a human-readable unique business number. */
    private function numero(string $prefixo): string
    {
        return sprintf('%s-%s-%s', $prefixo, date('YmdHis'), strtoupper(bin2hex(random_bytes(3))));
    }

    /** Persist an audit event. */
    private function auditar(Usuario $usuario, string $acao, string $modulo, EntityInterface $registro): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => $modulo,
            'registro_id' => $registro->get('id'), 'dados_posteriores' => $registro->toArray(),
        ]));
    }
}
