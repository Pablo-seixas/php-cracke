<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\AgendaService;
use Cake\Http\Response;
use DomainException;

final class AgendaController extends AppController
{
    /** Display appointments ordered chronologically. */
    public function index(): void
    {
        $table = $this->fetchTable('Consultas');
        $this->Authorization->authorize($table, 'index');
        $query = $table->find()->contain(['Pacientes', 'Profissionais'])->orderByAsc('Consultas.inicio');
        $this->set('consultas', $this->paginate($query));
    }

    /** Schedule an appointment. */
    public function add(): ?Response
    {
        $table = $this->fetchTable('Consultas');
        $this->Authorization->authorize($table, 'add');
        if ($this->request->is('post')) {
            try {
                (new AgendaService())->agendar($this->request->getData(), $this->usuarioAtual());
                $this->Flash->success('Consulta agendada.');

                return $this->redirect(['action' => 'index']);
            } catch (DomainException $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $this->set('pacientes', $this->fetchTable('Pacientes')->find('list', keyField: 'id', valueField: 'nome_completo')->where(['ativo' => true])->all());
        $this->set('profissionais', $this->fetchTable('Profissionais')->find('list', keyField: 'id', valueField: 'nome')->where(['ativo' => true])->all());

        return null;
    }

    /** Confirm, cancel or mark a patient absent. */
    public function status(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $consulta = $this->fetchTable('Consultas')->get($id);
        $this->Authorization->authorize($consulta, 'edit');
        (new AgendaService())->alterarStatus(
            $consulta,
            (string)$this->request->getData('status'),
            $this->usuarioAtual(),
            (string)$this->request->getData('motivo'),
        );
        $this->Flash->success('Status atualizado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
