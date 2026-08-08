<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CompraService;
use Cake\Http\Response;
use Cake\I18n\Date;
use DomainException;

final class ComprasController extends AppController
{
    /** List issued purchase orders. */
    public function index(): void
    {
        $table = $this->fetchTable('Compras');
        $this->Authorization->authorize($table, 'index');
        $this->set(
            'compras',
            $this->paginate($table->find()->contain(['Fornecedores'])->orderByDesc('Compras.created')),
        );
    }

    /** Display an order and received quantities. */
    public function view(int $id): void
    {
        $compra = $this->fetchTable('Compras')->get($id, contain: [
            'Fornecedores', 'Usuarios', 'ComprasItens.Medicamentos', 'ComprasItens.Produtos', 'ContasPagar',
        ]);
        $this->Authorization->authorize($compra, 'view');
        $this->set(compact('compra'));
    }

    /** Receive one order item and create stock/financial records. */
    public function receber(int $id): ?Response
    {
        $compra = $this->fetchTable('Compras')->get($id, contain: ['ComprasItens.Medicamentos', 'ComprasItens.Produtos']);
        $this->Authorization->authorize($compra, 'receber');
        if ($this->request->is('post')) {
            try {
                $vencimento = $this->request->getData('vencimento');
                (new CompraService())->receberPedido($compra, [[
                    'compra_item_id' => (int)$this->request->getData('compra_item_id'),
                    'quantidade' => $this->request->getData('quantidade'),
                    'numero_lote' => $this->request->getData('numero_lote'),
                    'data_fabricacao' => $this->request->getData('data_fabricacao'),
                    'data_validade' => $this->request->getData('data_validade'),
                ]], $this->usuarioAtual(), $vencimento ? new Date((string)$vencimento) : null);
                $this->Flash->success('Recebimento e entrada no estoque registrados.');

                return $this->redirect(['action' => 'view', $id]);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set(compact('compra'));

        return null;
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
