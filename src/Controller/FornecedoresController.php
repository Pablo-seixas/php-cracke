<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\FornecedorService;
use Cake\Http\Response;
use Throwable;

final class FornecedoresController extends AppController
{
    /** List suppliers with text search. */
    public function index(): void
    {
        $table = $this->fetchTable('Fornecedores');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()->where(['deleted IS' => null]);
        $busca = trim((string)$this->request->getQuery('busca'));
        if ($busca !== '') {
            $query->where(['OR' => [
                'razao_social ILIKE' => "%{$busca}%", 'nome_fantasia ILIKE' => "%{$busca}%",
                'cnpj ILIKE' => "%{$busca}%",
            ]]);
        }
        $this->set('fornecedores', $this->paginate($query->orderByAsc('razao_social')));
        $this->set(compact('busca'));
    }

    /** Create a supplier. */
    public function add(): ?Response
    {
        return $this->salvar();
    }

    /** Edit a supplier. */
    public function edit(int $id): ?Response
    {
        return $this->salvar($id);
    }

    /** Soft-delete a supplier. */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $table = $this->fetchTable('Fornecedores');
        $fornecedor = $table->get($id);
        $this->Authorization->authorize($fornecedor, 'inativar');
        (new FornecedorService())->inativar($fornecedor, $this->usuarioAtual());
        $this->Flash->success('Fornecedor inativado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Handle create and edit forms through the supplier service. */
    private function salvar(?int $id = null): ?Response
    {
        $table = $this->fetchTable('Fornecedores');
        $fornecedor = $id === null ? $table->newEmptyEntity() : $table->get($id);
        $this->Authorization->authorize($id === null ? $table : $fornecedor, $id === null ? 'add' : 'edit');
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                (new FornecedorService())->salvar($fornecedor, $this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Fornecedor salvo.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable) {
                $this->Flash->error('Não foi possível salvar. Verifique CNPJ e campos obrigatórios.');
            }
        }
        $this->set(compact('fornecedor'));
        $this->render($id === null ? 'add' : 'edit');

        return null;
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
