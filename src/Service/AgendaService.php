<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\HorarioIndisponivelException;
use App\Model\Entity\Consulta;
use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use DomainException;

final class AgendaService
{
    /** Schedule an appointment after checking every overlapping interval. */
    public function agendar(array $dados, Usuario $usuario): Consulta
    {
        $inicio = new DateTime((string)($dados['inicio'] ?? ''));
        $fim = new DateTime((string)($dados['fim'] ?? ''));
        if ($fim <= $inicio) {
            throw new HorarioIndisponivelException('O horário final deve ser posterior ao inicial.');
        }
        $profissionalId = (int)($dados['profissional_id'] ?? 0);
        $this->verificarDisponibilidade($profissionalId, $inicio, $fim);

        return ConnectionManager::get('default')->transactional(function () use (
            $dados,
            $usuario,
            $inicio,
            $fim,
        ): Consulta {
            $consultas = TableRegistry::getTableLocator()->get('Consultas');
            $consulta = $consultas->newEntity($dados + [
                'inicio' => $inicio, 'fim' => $fim, 'status' => 'agendada',
            ]);
            $consultas->saveOrFail($consulta);
            $this->auditar($usuario, 'agendamento', $consulta);

            return $consulta;
        });
    }

    /** Reschedule an existing appointment with the same overlap guarantees. */
    public function reagendar(Consulta $consulta, DateTime $inicio, DateTime $fim, Usuario $usuario): void
    {
        if (in_array($consulta->status, ['finalizada', 'cancelada'], true)) {
            throw new DomainException('Consulta finalizada ou cancelada não pode ser reagendada.');
        }
        $this->verificarDisponibilidade((int)$consulta->profissional_id, $inicio, $fim, $consulta->id);
        $consulta->inicio = $inicio;
        $consulta->fim = $fim;
        $consulta->status = 'agendada';
        TableRegistry::getTableLocator()->get('Consultas')->saveOrFail($consulta);
        $this->auditar($usuario, 'reagendamento', $consulta);
    }

    /** Confirm or cancel a scheduled appointment. */
    public function alterarStatus(Consulta $consulta, string $status, Usuario $usuario, ?string $motivo = null): void
    {
        $permitidos = ['confirmada', 'cancelada', 'paciente_ausente'];
        if (!in_array($status, $permitidos, true) || $consulta->status === 'finalizada') {
            throw new DomainException('Transição de consulta inválida.');
        }
        if ($status === 'cancelada' && trim((string)$motivo) === '') {
            throw new DomainException('Informe o motivo do cancelamento.');
        }
        $consulta->status = $status;
        $consulta->confirmado_em = $status === 'confirmada' ? DateTime::now() : $consulta->confirmado_em;
        $consulta->cancelado_em = $status === 'cancelada' ? DateTime::now() : null;
        if ($motivo) {
            $consulta->observacoes = trim(($consulta->observacoes ?: '') . "\n" . $motivo);
        }
        TableRegistry::getTableLocator()->get('Consultas')->saveOrFail($consulta);
        $this->auditar($usuario, $status, $consulta);
    }

    /** Reject consultation and agenda-block overlaps. */
    private function verificarDisponibilidade(
        int $profissionalId,
        DateTime $inicio,
        DateTime $fim,
        ?int $ignorarConsultaId = null,
    ): void {
        $consultas = TableRegistry::getTableLocator()->get('Consultas')->find()->where([
            'profissional_id' => $profissionalId,
            'status !=' => 'cancelada',
            'inicio <' => $fim,
            'fim >' => $inicio,
        ]);
        if ($ignorarConsultaId !== null) {
            $consultas->where(['id !=' => $ignorarConsultaId]);
        }
        $bloqueado = TableRegistry::getTableLocator()->get('Agendas')->exists([
            'profissional_id' => $profissionalId,
            'tipo' => 'bloqueado',
            'inicio <' => $fim,
            'fim >' => $inicio,
        ]);
        if ($consultas->count() > 0 || $bloqueado) {
            throw new HorarioIndisponivelException('O profissional já possui compromisso nesse intervalo.');
        }
    }

    /** Persist a scheduling audit event. */
    private function auditar(Usuario $usuario, string $acao, Consulta $consulta): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id, 'acao' => $acao, 'modulo' => 'agenda',
            'registro_id' => $consulta->id, 'dados_posteriores' => $consulta->toArray(),
        ]));
    }
}
