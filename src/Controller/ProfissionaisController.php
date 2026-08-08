<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Service\CadastroClinicoService;
use Cake\Http\Response;
use Throwable;

final class ProfissionaisController extends AppController
{
    /** List professionals. */
    public function index(): void
    {
        $table = $this->fetchTable('Profissionais');
        $this->Authorization->authorize($table, 'index');
        $this->set('profissionais', $this->paginate(
            $table->find()->contain(['Especialidades'])
                ->where(['Profissionais.deleted IS' => null])
                ->orderByAsc('Profissionais.nome'),
        ));
    }

    /** Create a professional. */
    public function add(): ?Response
    {
        return $this->salvar();
    }

    /** Edit a professional. */
    public function edit(int $id): ?Response
    {
        return $this->salvar($id);
    }

    /** Soft-delete a professional. */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $profissional = $this->fetchTable('Profissionais')->get($id);
        $this->Authorization->authorize($profissional, 'inativar');
        (new CadastroClinicoService())->inativar($profissional, $this->usuarioAtual(), 'profissionais');
        $this->Flash->success('Profissional inativado.');

        return $this->redirect(['action' => 'index']);
    }

    /** Handle professional forms. */
    private function salvar(?int $id = null): ?Response
    {
        $table = $this->fetchTable('Profissionais');
        $profissional = $id === null ? $table->newEmptyEntity() : $table->get($id);
        $this->Authorization->authorize($id === null ? $table : $profissional, $id === null ? 'add' : 'edit');
        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                $dados = $this->request->getData();
                $dados['dias_atendimento'] = array_values(array_filter((array)($dados['dias_atendimento'] ?? [])));
                $dados['horarios'] = ['inicio' => $dados['horario_inicio'] ?? null, 'fim' => $dados['horario_fim'] ?? null];
                (new CadastroClinicoService())->salvarProfissional($profissional, $dados, $this->usuarioAtual());
                $this->Flash->success('Profissional salvo.');

                return $this->redirect(['action' => 'index']);
            } catch (Throwable) {
                $this->Flash->error('Não foi possível salvar. Verifique CPF e campos obrigatórios.');
            }
        }
        $this->set('especialidades', $table->Especialidades->find('list')->where(['ativo' => true])->all());
        $this->set(compact('profissional'));
        $this->render($id === null ? 'add' : 'edit');

        return null;
    }

    /** Return the authenticated operator. */
    private function usuarioAtual(): Usuario
    {
        return $this->request->getAttribute('identity')->getOriginalData();
    }
}
