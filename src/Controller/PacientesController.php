<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CadastroClinicoService;
use Cake\Http\Response;
use Throwable;

final class PacientesController extends AppController
{
    /** List active patients with privacy-conscious search. */
    public function index(): void
    {
        $table = $this->fetchTable('Pacientes');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()->where(['deleted IS' => null]);
        $busca = trim((string)$this->request->getQuery('busca'));
        if ($busca !== '') {
            $query->where(['OR' => ['nome_completo ILIKE' => "%{$busca}%", 'nome_social ILIKE' => "%{$busca}%"]]);
        }
        $this->set('pacientes', $this->paginate($query->orderByAsc('nome_completo')));
        $this->set(compact('busca'));
    }

    /** Display a patient and related history. */
    public function view(int $id): void
    {
        $paciente = $this->fetchTable('Pacientes')->get($id, contain: ['Consultas.Profissionais', 'Atendimentos']);
        $this->Authorization->authorize($paciente, 'view');
        $this->set(compact('paciente'));
    }

    /** Create a patient. */
    public function add(): ?Response
    {
        return $this->salvar();
    }

    /** Edit a patient. */
    public function edit(int $id): ?Response
    {
        return $this->salvar($id);
    }

    /** Soft-delete a patient. */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $paciente = $this->fetchTable('Pacientes')->get($id);
        $this->Authorization->authorize($paciente, 'inativar');
        (new CadastroClinicoService())->inativar($paciente, $this->usuarioAtual(), 'pacientes');
        $this->Flash->success('Paciente inativado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Handle patient forms. */
    private function salvar(?int $id = null): ?Response
    {
        $table = $this->fetchTable('Pacientes');
        $paciente = $id === null ? $table->newEmptyEntity() : $table->get($id);
        $this->Authorization->authorize($id === null ? $table : $paciente, $id === null ? 'add' : 'edit');
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                (new CadastroClinicoService())->salvarPaciente($paciente, $this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Paciente salvo.');

                return $this->redirect(['action' => 'view', $paciente->id]);
            } catch (Throwable) {
                $this->Flash->error('Não foi possível salvar. Verifique CPF e campos obrigatórios.');
            }
        }
        $this->set(compact('paciente'));
        $this->render($id === null ? 'add' : 'edit');

        return null;
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
