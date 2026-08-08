<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Exception\HorarioIndisponivelException;
use App\Service\AgendaService;
use App\Service\CadastroClinicoService;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class AgendaClinicaServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios', 'app.Clientes', 'app.Pacientes', 'app.Especialidades',
        'app.Profissionais', 'app.Agendas', 'app.Consultas', 'app.Auditorias',
    ];

    /** Create patient/professional records with normalized valid CPFs. */
    public function testCadastroClinicoAuditado(): void
    {
        [$usuario, $especialidade] = $this->referencias();
        $service = new CadastroClinicoService();
        $pacientes = TableRegistry::getTableLocator()->get('Pacientes');
        $paciente = $service->salvarPaciente($pacientes->newEmptyEntity(), [
            'nome_completo' => 'Paciente Fictício', 'cpf' => '529.982.247-25',
            'data_nascimento' => '1990-01-01', 'ativo' => true,
        ], $usuario);
        $profissionais = TableRegistry::getTableLocator()->get('Profissionais');
        $profissional = $service->salvarProfissional($profissionais->newEmptyEntity(), [
            'especialidade_id' => $especialidade->id, 'nome' => 'Profissional Fictício',
            'cpf' => '111.444.777-35', 'tipo' => 'medico', 'registro_profissional' => 'CRM-TESTE',
            'dias_atendimento' => ['segunda'], 'horarios' => ['inicio' => '08:00', 'fim' => '17:00'],
            'ativo' => true,
        ], $usuario);

        $this->assertSame('52998224725', $paciente->cpf);
        $this->assertSame('11144477735', $profissional->cpf);
        $this->assertSame(2, TableRegistry::getTableLocator()->get('Auditorias')->find()->count());
    }

    /** Reject partial overlap with another consultation. */
    public function testImpedeSobreposicaoDeConsulta(): void
    {
        [$usuario, , $paciente, $profissional] = $this->cenarioAgenda();
        $service = new AgendaService();
        $service->agendar([
            'paciente_id' => $paciente->id, 'profissional_id' => $profissional->id,
            'inicio' => '2030-03-10 10:00:00', 'fim' => '2030-03-10 11:00:00',
            'motivo' => 'Consulta inicial',
        ], $usuario);

        $this->expectException(HorarioIndisponivelException::class);
        $service->agendar([
            'paciente_id' => $paciente->id, 'profissional_id' => $profissional->id,
            'inicio' => '2030-03-10 10:30:00', 'fim' => '2030-03-10 11:30:00',
            'motivo' => 'Conflito parcial',
        ], $usuario);
    }

    /** Reject an appointment inside a blocked agenda interval. */
    public function testRespeitaBloqueioDeAgenda(): void
    {
        [$usuario, , $paciente, $profissional] = $this->cenarioAgenda();
        $agendas = TableRegistry::getTableLocator()->get('Agendas');
        $agendas->saveOrFail($agendas->newEntity([
            'profissional_id' => $profissional->id,
            'inicio' => new DateTime('2030-03-10 14:00:00'),
            'fim' => new DateTime('2030-03-10 15:00:00'),
            'tipo' => 'bloqueado', 'motivo' => 'Reunião',
        ]));

        $this->expectException(HorarioIndisponivelException::class);
        (new AgendaService())->agendar([
            'paciente_id' => $paciente->id, 'profissional_id' => $profissional->id,
            'inicio' => '2030-03-10 14:30:00', 'fim' => '2030-03-10 14:45:00',
        ], $usuario);
    }

    /** @return array{0: object, 1: object} */
    private function referencias(): array
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->newEntity([
            'nome' => 'Operador Clínico', 'email' => uniqid('clinica-') . '@example.test',
            'senha' => 'ValidPassword!123', 'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        $especialidades = TableRegistry::getTableLocator()->get('Especialidades');
        $especialidade = $especialidades->newEntity(['nome' => uniqid('Clínica '), 'ativo' => true]);
        $especialidades->saveOrFail($especialidade);

        return [$usuario, $especialidade];
    }

    /** @return array{0: object, 1: object, 2: object, 3: object} */
    private function cenarioAgenda(): array
    {
        [$usuario, $especialidade] = $this->referencias();
        $cadastro = new CadastroClinicoService();
        $pacientes = TableRegistry::getTableLocator()->get('Pacientes');
        $paciente = $cadastro->salvarPaciente($pacientes->newEmptyEntity(), [
            'nome_completo' => 'Paciente Agenda', 'cpf' => '52998224725',
            'data_nascimento' => '1990-01-01', 'ativo' => true,
        ], $usuario);
        $profissionais = TableRegistry::getTableLocator()->get('Profissionais');
        $profissional = $cadastro->salvarProfissional($profissionais->newEmptyEntity(), [
            'especialidade_id' => $especialidade->id, 'nome' => 'Profissional Agenda',
            'cpf' => '11144477735', 'tipo' => 'medico', 'dias_atendimento' => ['domingo'],
            'horarios' => ['inicio' => '08:00', 'fim' => '18:00'], 'ativo' => true,
        ], $usuario);

        return [$usuario, $especialidade, $paciente, $profissional];
    }
}
