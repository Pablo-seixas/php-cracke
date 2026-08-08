<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CaixaService;
use Cake\Http\Response;
use DomainException;

final class CaixasController extends AppController
{
    /** List registers and their immutable movements. */
    public function index(): void
    {
        $table = $this->fetchTable('Caixas');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()->contain(['UsuarioAberturas', 'UsuarioFechamentos'])->orderByDesc('Caixas.created');
        $this->set('caixas', $this->paginate($query));
    }

    /** Open a register. */
    public function abrir(): ?Response
    {
        $table = $this->fetchTable('Caixas');
        $this->Authorization->authorize($table, 'add');
        if ($this->request->is('post')) {
            try {
                (new CaixaService())->abrir(
                    $this->usuarioAtual(),
                    (float)$this->request->getData('saldo_inicial'),
                    (string)$this->request->getData('observacoes'),
                );
                $this->Flash->success('Caixa aberto.');

                return $this->redirect(['action' => 'index']);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }

        return null;
    }

    /** Register a supply or withdrawal. */
    public function movimentar(int $id): ?Response
    {
        $caixa = $this->fetchTable('Caixas')->get($id);
        $this->Authorization->authorize($caixa, 'movimentar');
        if ($this->request->is('post')) {
            try {
                (new CaixaService())->movimentar(
                    $caixa,
                    $this->usuarioAtual(),
                    (string)$this->request->getData('tipo'),
                    (float)$this->request->getData('valor'),
                    (string)$this->request->getData('motivo'),
                );
                $this->Flash->success('Movimentação registrada.');

                return $this->redirect(['action' => 'index']);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set(compact('caixa'));

        return null;
    }

    /** Close a register. */
    public function fechar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $caixa = $this->fetchTable('Caixas')->get($id);
        $this->Authorization->authorize($caixa, 'fechar');
        (new CaixaService())->fechar($caixa, $this->usuarioAtual());
        $this->Flash->success('Caixa fechado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
