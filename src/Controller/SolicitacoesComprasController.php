<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CompraService;
use Cake\Http\Response;
use DomainException;

final class SolicitacoesComprasController extends AppController
{
    /** List purchase requests and their current workflow state. */
    public function index(): void
    {
        $table = $this->fetchTable('SolicitacoesCompras');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()
            ->contain(['Solicitantes', 'Aprovadores', 'FornecedoresEscolhidos'])
            ->orderByDesc('SolicitacoesCompras.created');
        $this->set('solicitacoes', $this->paginate($query));
    }

    /** Display one request with items and quotations. */
    public function view(int $id): void
    {
        $solicitacao = $this->fetchTable('SolicitacoesCompras')->get($id, contain: [
            'Solicitantes', 'Aprovadores', 'FornecedoresEscolhidos',
            'SolicitacoesComprasItens.Medicamentos', 'SolicitacoesComprasItens.Produtos',
            'Cotacoes.Fornecedores', 'Cotacoes.CotacoesItens',
        ]);
        $this->Authorization->authorize($solicitacao, 'view');
        $this->set(compact('solicitacao'));
    }

    /** Create and immediately submit a simple purchase request. */
    public function add(): ?Response
    {
        $table = $this->fetchTable('SolicitacoesCompras');
        $this->Authorization->authorize($table, 'add');
        if ($this->request->is('post')) {
            try {
                $dados = $this->request->getData();
                $item = [
                    'medicamento_id' => $dados['medicamento_id'] ?: null,
                    'produto_id' => $dados['produto_id'] ?: null,
                    'quantidade' => $dados['quantidade'],
                    'preco_estimado' => $dados['preco_estimado'],
                    'observacoes' => $dados['observacoes'] ?? null,
                ];
                $service = new CompraService();
                $solicitacao = $service->criarSolicitacao([
                    'justificativa' => $dados['justificativa'],
                ], [$item], $this->usuarioAtual());
                $service->enviar($solicitacao, $this->usuarioAtual());
                $this->Flash->success('Solicitação enviada para cotação.');

                return $this->redirect(['action' => 'view', $solicitacao->id]);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set('medicamentos', $this->fetchTable('Medicamentos')->find('list')->where(['ativo' => true])->all());
        $this->set('produtos', $this->fetchTable('Produtos')->find('list', keyField: 'id', valueField: 'nome')->where(['ativo' => true])->all());

        return null;
    }

    /** Register a supplier quotation for every request item. */
    public function cotar(int $id): ?Response
    {
        $table = $this->fetchTable('SolicitacoesCompras');
        $solicitacao = $table->get($id, contain: ['SolicitacoesComprasItens']);
        $this->Authorization->authorize($solicitacao, 'cotar');
        if ($this->request->is('post')) {
            try {
                $itens = [];
                foreach ($solicitacao->solicitacoes_compras_itens as $item) {
                    $preco = $this->request->getData("precos.{$item->id}");
                    $itens[] = [
                        'solicitacao_compra_item_id' => $item->id,
                        'quantidade' => $item->quantidade,
                        'preco_unitario' => $preco,
                    ];
                }
                (new CompraService())->cotar(
                    $solicitacao,
                    (int)$this->request->getData('fornecedor_id'),
                    $this->request->getData(),
                    $itens,
                    $this->usuarioAtual(),
                );
                $this->Flash->success('Cotação registrada.');

                return $this->redirect(['action' => 'view', $id]);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set('fornecedores', $this->fetchTable('Fornecedores')->find('list')->where(['ativo' => true])->all());
        $this->set(compact('solicitacao'));

        return null;
    }

    /** Select a quotation and move the request to approval. */
    public function selecionar(int $id, int $cotacaoId): Response
    {
        $this->request->allowMethod(['post']);
        $solicitacao = $this->fetchTable('SolicitacoesCompras')->get($id);
        $this->Authorization->authorize($solicitacao, 'cotar');
        $cotacao = $this->fetchTable('Cotacoes')->get($cotacaoId);
        (new CompraService())->selecionarCotacao($solicitacao, $cotacao, $this->usuarioAtual());
        $this->Flash->success('Cotação enviada para aprovação.');

        return $this->redirect(['action' => 'view', $id]);
    }

    /** Approve or reject a request. */
    public function decidir(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $solicitacao = $this->fetchTable('SolicitacoesCompras')->get($id);
        $this->Authorization->authorize($solicitacao, 'aprovar');
        $aprovar = $this->request->getData('decisao') === 'aprovar';
        (new CompraService())->decidir(
            $solicitacao,
            $this->usuarioAtual(),
            $aprovar,
            (string)$this->request->getData('motivo'),
        );
        $this->Flash->success($aprovar ? 'Compra aprovada.' : 'Compra rejeitada.');

        return $this->redirect(['action' => 'view', $id]);
    }

    /** Emit the approved purchase order. */
    public function emitir(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $solicitacao = $this->fetchTable('SolicitacoesCompras')->get($id);
        $this->Authorization->authorize($solicitacao, 'emitir');
        $compra = (new CompraService())->emitirPedido($solicitacao, $this->usuarioAtual());
        $this->Flash->success('Pedido emitido.');

        return $this->redirect(['controller' => 'Compras', 'action' => 'view', $compra->id]);
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
