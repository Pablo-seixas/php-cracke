<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\ProntuarioService;
use Cake\Http\Response;
use Throwable;

final class ProntuariosController extends AppController
{
    /** Display a patient record and register its access. */
    public function view(int $pacienteId): void
    {
        $this->Authorization->skipAuthorization();
        $this->set('prontuario', (new ProntuarioService())->visualizar($pacienteId, $this->usuarioAtual()));
    }

    /** Create or edit a versioned patient record. */
    public function edit(int $pacienteId): ?Response
    {
        $this->Authorization->skipAuthorization();
        $service = new ProntuarioService();
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                $service->salvar($pacienteId, $this->request->getData(), $this->usuarioAtual(), (string)$this->request->getData('motivo_alteracao'));
                $this->Flash->success('Prontuário salvo e versionado.');

                return $this->redirect(['action' => 'view', $pacienteId]);
            } catch (Throwable $exception) {
                $this->Flash->error($exception->getMessage());
            }
        }
        $table = $this->fetchTable('Prontuarios');
        $prontuario = $table->find()->where(['paciente_id' => $pacienteId])->first() ?? $table->newEntity(['paciente_id' => $pacienteId]);
        $this->set(compact('prontuario', 'pacienteId'));

        return null;
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
