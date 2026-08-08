<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\AcessoProntuarioNegadoException;
use App\Model\Entity\Prontuario;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

final class ProntuarioService
{
    /** Load a clinical record and audit every view. */
    public function visualizar(int $pacienteId, Usuario $usuario): Prontuario
    {
        $this->autorizar($usuario, 'prontuarios.visualizar');
        $table = TableRegistry::getTableLocator()->get('Prontuarios');
        $prontuario = $table->find()
            ->contain(['Pacientes', 'ProntuariosHistoricos', 'ProntuariosHistoricos.Usuarios'])
            ->where(['Prontuarios.paciente_id' => $pacienteId])
            ->firstOrFail();
        $this->auditar($usuario, 'visualizacao', $prontuario);

        return $prontuario;
    }

    /** Create or version a record, preserving the complete previous state. */
    public function salvar(int $pacienteId, array $dados, Usuario $usuario, string $motivo = ''): Prontuario
    {
        $this->autorizar($usuario, 'prontuarios.editar');

        return ConnectionManager::get('default')->transactional(function () use (
            $pacienteId,
            $dados,
            $usuario,
            $motivo,
        ): Prontuario {
            $table = TableRegistry::getTableLocator()->get('Prontuarios');
            $prontuario = $table->find()->where(['paciente_id' => $pacienteId])->first();
            $antes = $prontuario?->toArray();
            $prontuario ??= $table->newEntity(['paciente_id' => $pacienteId, 'versao' => 1]);
            if (!$prontuario->isNew()) {
                $historicos = TableRegistry::getTableLocator()->get('ProntuariosHistoricos');
                $historicos->saveOrFail($historicos->newEntity([
                    'prontuario_id' => $prontuario->id, 'versao' => $prontuario->versao,
                    'dados' => $antes, 'usuario_id' => $usuario->id, 'motivo' => $motivo,
                ]));
                $prontuario->versao++;
            }
            $prontuario = $table->patchEntity($prontuario, $dados, [
                'accessibleFields' => ['paciente_id' => false, 'versao' => false],
            ]);
            $table->saveOrFail($prontuario);
            $this->auditar($usuario, $antes === null ? 'criacao' : 'alteracao', $prontuario, $antes);

            return $prontuario;
        });
    }

    /** Enforce least privilege even when the service is called outside a controller. */
    private function autorizar(Usuario $usuario, string $permissao): void
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->get($usuario->id, contain: ['Perfis.Permissoes']);
        if (!$usuario->isAdministrador() && !$usuario->possuiPermissao($permissao)) {
            throw new AcessoProntuarioNegadoException();
        }
    }

    /** Store clinical access/change audit evidence. */
    private function auditar(Usuario $usuario, string $acao, Prontuario $prontuario, ?array $antes = null): void
    {
        $table = TableRegistry::getTableLocator()->get('Auditorias');
        $table->saveOrFail($table->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => 'prontuarios',
            'registro_id' => $prontuario->id, 'dados_anteriores' => $antes,
            'dados_posteriores' => $prontuario->toArray(),
        ]));
    }
}
