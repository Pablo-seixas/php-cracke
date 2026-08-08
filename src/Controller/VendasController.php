<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\VendaService;
use Cake\Http\Response;
use DomainException;

final class VendasController extends AppController
{
    /** List sales history. */
    public function index(): void
    {
        $table = $this->fetchTable('Vendas');
        $this->Authorization->authorize($table, 'index');
        $this->set('vendas', $this->paginate($table->find()->contain(['Clientes', 'Usuarios'])->orderByDesc('Vendas.created')));
    }

    /** Display sale items and payments. */
    public function view(int $id): void
    {
        $venda = $this->fetchTable('Vendas')->get($id, contain: [
            'Clientes', 'Usuarios', 'VendasItens.Medicamentos', 'VendasItens.Produtos',
            'VendasItens.Lotes', 'Pagamentos',
        ]);
        $this->Authorization->authorize($venda, 'view');
        $this->set(compact('venda'));
    }

    /** Create a quick sale using the domain service. */
    public function add(): ?Response
    {
        $table = $this->fetchTable('Vendas');
        $this->Authorization->authorize($table, 'add');
        $usuario = $this->usuarioAtual();
        $caixa = $this->fetchTable('Caixas')->find()->where([
            'usuario_abertura_id' => $usuario->id, 'status' => 'aberto',
        ])->first();
        if ($this->request->is('post')) {
            try {
                if ($caixa === null) {
                    throw new DomainException('Abra seu caixa antes de vender.');
                }
                $dados = $this->request->getData();
                $venda = (new VendaService())->finalizar($dados, [[
                    'medicamento_id' => $dados['medicamento_id'] ?? null ?: null,
                    'produto_id' => $dados['produto_id'] ?? null ?: null,
                    'receita_id' => $dados['receita_id'] ?? null ?: null,
                    'quantidade' => $dados['quantidade'],
                ]], [[
                    'forma' => $dados['forma'], 'valor' => $dados['valor_pago'],
                    'referencia' => $dados['referencia'] ?? null,
                ]], $usuario, $caixa);
                $this->Flash->success('Venda finalizada.');

                return $this->redirect(['action' => 'view', $venda->id]);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set('clientes', $this->fetchTable('Clientes')->find('list')->where(['ativo' => true])->all());
        $this->set('medicamentos', $this->fetchTable('Medicamentos')->find('list')->where(['ativo' => true])->all());
        $this->set('produtos', $this->fetchTable('Produtos')->find('list', keyField: 'id', valueField: 'nome')->where(['ativo' => true])->all());
        $this->set('receitas', $this->fetchTable('Receitas')->find('list', keyField: 'id', valueField: 'numero')->all());
        $this->set(compact('caixa'));

        return null;
    }

    /** Cancel a sale and reverse stock/payment. */
    public function cancelar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $venda = $this->fetchTable('Vendas')->get($id);
        $this->Authorization->authorize($venda, 'cancelar');
        (new VendaService())->cancelar($venda, $this->usuarioAtual(), (string)$this->request->getData('motivo'));
        $this->Flash->success('Venda cancelada e estornada.');

        return $this->redirect(['action' => 'view', $id]);
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
