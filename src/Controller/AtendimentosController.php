<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\AtendimentoService;
use Cake\Http\Response;
use Throwable;

final class AtendimentosController extends AppController
{
    /** List recent encounters. */
    public function index(): void
    {
        $table = $this->fetchTable('Atendimentos');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()->contain(['Pacientes', 'Profissionais'])->orderByDesc('Atendimentos.iniciado_em');
        $this->set('atendimentos', $this->paginate($query));
    }

    /** Start an encounter. */
    public function add(): ?Response
    {
        $table = $this->fetchTable('Atendimentos');
        $this->Authorization->authorize($table, 'add');
        if ($this->request->is('post')) {
            try {
                (new AtendimentoService())->iniciar($this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Atendimento iniciado.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable $exception) {
                $this->Flash->error('Não foi possível iniciar: ' . $exception->getMessage());
            }
        }
        $this->set('pacientes', $this->fetchTable('Pacientes')->find('list', keyField: 'id', valueField: 'nome_completo')->where(['ativo' => true])->all());
        $this->set('profissionais', $this->fetchTable('Profissionais')->find('list', keyField: 'id', valueField: 'nome')->where(['ativo' => true])->all());

        return null;
    }

    /** Display encounter details. */
    public function view(int $id): void
    {
        $atendimento = $this->fetchTable('Atendimentos')->get($id, contain: ['Pacientes', 'Profissionais', 'Prescricoes']);
        $this->Authorization->authorize($atendimento, 'view');
        $this->set(compact('atendimento'));
    }

    /** Finish an encounter. */
    public function finalizar(int $id): Response
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $atendimento = $this->fetchTable('Atendimentos')->get($id);
        $this->Authorization->authorize($atendimento, 'edit');
        (new AtendimentoService())->finalizar($atendimento, $this->request->getData(), $this->usuarioAtual());
        $this->Flash->success('Atendimento finalizado.');

        return $this->redirect(['action' => 'view', $id]);
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
