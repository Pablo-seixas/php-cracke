<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CatalogoService;
use Cake\Http\Response;
use Throwable;

final class ProdutosController extends AppController
{
    /** List active products with search and category filters. */
    public function index(): void
    {
        $table = $this->fetchTable('Produtos');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()->contain(['Categorias', 'Fabricantes'])->where(['Produtos.deleted IS' => null]);
        $busca = trim((string)$this->request->getQuery('busca'));
        if ($busca !== '') {
            $query->where(['OR' => [
                'Produtos.nome ILIKE' => "%{$busca}%",
                'Produtos.codigo_interno ILIKE' => "%{$busca}%",
                'Produtos.codigo_barras ILIKE' => "%{$busca}%",
            ]]);
        }
        $categoriaId = $this->request->getQuery('categoria_id');
        if ($categoriaId !== null && $categoriaId !== '') {
            $query->where(['Produtos.categoria_id' => $categoriaId]);
        }
        $this->set('produtos', $this->paginate($query->orderByAsc('Produtos.nome')));
        $this->set('categorias', $table->Categorias->find('list')->where(['ativo' => true])->all());
        $this->set(compact('busca', 'categoriaId'));
    }

    /** Create a product. */
    public function add(): ?Response
    {
        return $this->salvar();
    }

    /** Edit a product. */
    public function edit(int $id): ?Response
    {
        return $this->salvar($id);
    }

    /** Soft-delete a product. */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $table = $this->fetchTable('Produtos');
        $produto = $table->get($id);
        $this->Authorization->authorize($produto, 'inativar');
        (new CatalogoService())->inativar($table, $produto, $this->usuarioAtual());
        $this->Flash->success('Produto inativado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Handle create and edit forms through the catalog service. */
    private function salvar(?int $id = null): ?Response
    {
        $table = $this->fetchTable('Produtos');
        $produto = $id === null ? $table->newEmptyEntity() : $table->get($id);
        $this->Authorization->authorize($id === null ? $table : $produto, $id === null ? 'add' : 'edit');
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                (new CatalogoService())->salvar($table, $produto, $this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Produto salvo.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable) {
                $this->Flash->error('Não foi possível salvar o produto. Verifique os campos.');
            }
        }
        $this->set('categorias', $table->Categorias->find('list')->where(['ativo' => true])->all());
        $this->set('fabricantes', $table->Fabricantes->find('list')->where(['ativo' => true])->all());
        $this->set(compact('produto'));
        $this->render($id === null ? 'add' : 'edit');

        return null;
    }

    /** Return the authenticated domain user. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
