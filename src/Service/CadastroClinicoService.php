<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Paciente;
use App\Model\Entity\Profissionai;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use DateTimeImmutable;

final class CadastroClinicoService
{
    /** Create or update a patient and audit sensitive-data changes. */
    public function salvarPaciente(Paciente $paciente, array $dados, Usuario $usuario): Paciente
    {
        $dados['cpf'] = $this->normalizarDocumento($dados['cpf'] ?? null);

        return ConnectionManager::get('default')->transactional(function () use (
            $paciente,
            $dados,
            $usuario,
        ): Paciente {
            $table = TableRegistry::getTableLocator()->get('Pacientes');
            $acao = $paciente->isNew() ? 'criacao' : 'alteracao';
            $antes = $paciente->isNew() ? null : $paciente->toArray();
            $paciente = $table->patchEntity($paciente, $dados);
            $table->saveOrFail($paciente);
            $this->auditar($usuario, $acao, 'pacientes', $paciente, $antes);

            return $paciente;
        });
    }

    /** Create or update a healthcare professional. */
    public function salvarProfissional(Profissionai $profissional, array $dados, Usuario $usuario): Profissionai
    {
        $dados['cpf'] = $this->normalizarDocumento($dados['cpf'] ?? null);
        $dados['dias_atendimento'] = $dados['dias_atendimento'] ?? [];
        $dados['horarios'] = $dados['horarios'] ?? [];

        return ConnectionManager::get('default')->transactional(function () use (
            $profissional,
            $dados,
            $usuario,
        ): Profissionai {
            $table = TableRegistry::getTableLocator()->get('Profissionais');
            $acao = $profissional->isNew() ? 'criacao' : 'alteracao';
            $antes = $profissional->isNew() ? null : $profissional->toArray();
            $profissional = $table->patchEntity($profissional, $dados);
            $table->saveOrFail($profissional);
            $this->auditar($usuario, $acao, 'profissionais', $profissional, $antes);

            return $profissional;
        });
    }

    /** Soft-delete clinical registration without losing history. */
    public function inativar(object $registro, Usuario $usuario, string $modulo): void
    {
        $table = TableRegistry::getTableLocator()->get($modulo === 'pacientes' ? 'Pacientes' : 'Profissionais');
        $antes = $registro->toArray();
        $registro->ativo = false;
        $registro->deleted = new DateTimeImmutable();
        $table->saveOrFail($registro);
        $this->auditar($usuario, 'inativacao', $modulo, $registro, $antes);
    }

    /** Normalize optional document values to digits only. */
    private function normalizarDocumento(mixed $valor): ?string
    {
        $documento = preg_replace('/\D/', '', (string)$valor) ?? '';

        return $documento !== '' ? $documento : null;
    }

    /** Persist a clinical registration audit. */
    private function auditar(Usuario $usuario, string $acao, string $modulo, object $registro, ?array $antes): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => $modulo,
            'registro_id' => $registro->id, 'dados_anteriores' => $antes,
            'dados_posteriores' => $registro->toArray(),
        ]));
    }
}
