<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CatalogoService;
use Cake\Http\Response;
use Throwable;

final class CategoriasController extends AppController
{
    /** List active categories. */
    public function index(): void
    {
        $table = $this->fetchTable('Categorias');
        $this->Authorization->authorize($table, 'index');
        $this->set('categorias', $this->paginate($table->find()->where(['deleted IS' => null])->orderByAsc('nome')));
    }

    /** Create a category. */
    public function add(): ?Response
    {
        return $this->salvar();
    }

    /** Edit a category. */
    public function edit(int $id): ?Response
    {
        return $this->salvar($id);
    }

    /** Soft-delete a category. */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $table = $this->fetchTable('Categorias');
        $entity = $table->get($id);
        $this->Authorization->authorize($entity, 'inativar');
        (new CatalogoService())->inativar($table, $entity, $this->usuarioAtual());
        $this->Flash->success('Categoria inativada.');

        return $this->redirect(['action' => 'index']);
    }

    /** Handle category forms through the catalog service. */
    private function salvar(?int $id = null): ?Response
    {
        $table = $this->fetchTable('Categorias');
        $entity = $id === null ? $table->newEmptyEntity() : $table->get($id);
        $this->Authorization->authorize($id === null ? $table : $entity, $id === null ? 'add' : 'edit');
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                (new CatalogoService())->salvar($table, $entity, $this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Categoria salva.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable) {
                $this->Flash->error('Não foi possível salvar a categoria. Verifique os campos.');
            }
        }
        $this->set('categoria', $entity);
        $this->render($id === null ? 'add' : 'edit');

        return null;
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
