<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CatalogoService;
use Cake\Http\Response;
use Throwable;

final class EspecialidadesController extends AppController
{
    /** List clinical specialties. */
    public function index(): void
    {
        $table = $this->fetchTable('Especialidades');
        $this->Authorization->authorize($table, 'index');
        $this->set('especialidades', $this->paginate($table->find()->orderByAsc('nome')));
    }

    /** Create a specialty. */
    public function add(): ?Response
    {
        return $this->salvar();
    }

    /** Edit a specialty. */
    public function edit(int $id): ?Response
    {
        return $this->salvar($id);
    }

    /** Handle specialty forms. */
    private function salvar(?int $id = null): ?Response
    {
        $table = $this->fetchTable('Especialidades');
        $especialidade = $id === null ? $table->newEmptyEntity() : $table->get($id);
        $this->Authorization->authorize($id === null ? $table : $especialidade, $id === null ? 'add' : 'edit');
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                (new CatalogoService())->salvar($table, $especialidade, $this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Especialidade salva.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable) {
                $this->Flash->error('Não foi possível salvar a especialidade.');
            }
        }
        $this->set(compact('especialidade'));
        $this->render($id === null ? 'add' : 'edit');

        return null;
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
