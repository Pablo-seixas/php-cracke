<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Model\Table\PerfisTable;
use App\Service\PerfilService;
use Cake\Http\Response;
use LogicException;

final class PerfisController extends AppController
{
    protected PerfisTable $Perfis;

    /**
     * Load the profiles table.
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Perfis = $this->fetchTable('Perfis');
    }

    /**
     * List profiles and assigned permissions.
     */
    public function index(): void
    {
        $this->Authorization->authorize($this->Perfis, 'index');
        $this->set('perfis', $this->Perfis->find()->contain(['Permissoes'])->orderBy(['nome' => 'ASC'])->all());
    }

    /**
     * Edit a profile and its permission assignments.
     */
    public function edit(int $id): ?Response
    {
        $perfil = $this->Perfis->get($id, contain: ['Permissoes']);
        $this->Authorization->authorize($perfil, 'edit');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $identity = $this->request->getAttribute('identity');
            $responsavel = $identity?->getOriginalData();
            if (!$responsavel instanceof Usuario) {
                throw new LogicException('Identidade autenticada inválida.');
            }
            $perfil = (new PerfilService($this->Perfis))->atualizar(
                $perfil,
                $this->request->getData(),
                $responsavel,
                $this->request->clientIp(),
                $this->request->getHeaderLine('User-Agent') ?: null,
            );
            $this->Flash->success('Perfil e permissões atualizados com sucesso.');

            return $this->redirect(['action' => 'index']);
        }
        $this->set('permissoes', $this->Perfis->Permissoes->find('list')->orderBy(['codigo' => 'ASC'])->all());
        $this->set(compact('perfil'));

        return null;
    }
}
