<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Atendimento;
use App\Model\Entity\Prescrico;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

final class AtendimentoService
{
    /** Start an encounter and link it to its scheduled consultation. */
    public function iniciar(array $dados, Usuario $usuario): Atendimento
    {
        return ConnectionManager::get('default')->transactional(function () use ($dados, $usuario): Atendimento {
            $table = TableRegistry::getTableLocator()->get('Atendimentos');
            $dados['iniciado_em'] ??= new DateTime();
            $dados['status'] = 'em_atendimento';
            $atendimento = $table->newEntity($dados);
            $table->saveOrFail($atendimento);
            if ($atendimento->consulta_id !== null) {
                $consultas = TableRegistry::getTableLocator()->get('Consultas');
                $consulta = $consultas->get($atendimento->consulta_id);
                $consulta->status = 'em_atendimento';
                $consultas->saveOrFail($consulta);
            }
            $this->auditar($usuario, 'criacao', 'atendimentos', $atendimento);

            return $atendimento;
        });
    }

    /** Finish an encounter without generating an automatic diagnosis. */
    public function finalizar(Atendimento $atendimento, array $dados, Usuario $usuario): Atendimento
    {
        return ConnectionManager::get('default')->transactional(function () use (
            $atendimento,
            $dados,
            $usuario,
        ): Atendimento {
            $table = TableRegistry::getTableLocator()->get('Atendimentos');
            $antes = $atendimento->toArray();
            unset($dados['diagnostico']);
            $dados['status'] = 'finalizado';
            $dados['finalizado_em'] ??= new DateTime();
            $atendimento = $table->patchEntity($atendimento, $dados);
            $table->saveOrFail($atendimento);
            if ($atendimento->consulta_id !== null) {
                $consultas = TableRegistry::getTableLocator()->get('Consultas');
                $consulta = $consultas->get($atendimento->consulta_id);
                $consulta->status = 'finalizada';
                $consultas->saveOrFail($consulta);
            }
            $this->auditar($usuario, 'finalizacao', 'atendimentos', $atendimento, $antes);

            return $atendimento;
        });
    }

    /** Add a professional-authored prescription to an encounter. */
    public function prescrever(array $dados, Usuario $usuario): Prescrico
    {
        $table = TableRegistry::getTableLocator()->get('Prescricoes');
        $prescricao = $table->newEntity($dados);
        $table->saveOrFail($prescricao);
        $this->auditar($usuario, 'criacao', 'prescricoes', $prescricao);

        return $prescricao;
    }

    /** Store immutable audit evidence. */
    private function auditar(
        Usuario $usuario,
        string $acao,
        string $modulo,
        object $registro,
        ?array $antes = null,
    ): void {
        $table = TableRegistry::getTableLocator()->get('Auditorias');
        $table->saveOrFail($table->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => $modulo,
            'registro_id' => $registro->id, 'dados_anteriores' => $antes,
            'dados_posteriores' => $registro->toArray(),
        ]));
    }
}
