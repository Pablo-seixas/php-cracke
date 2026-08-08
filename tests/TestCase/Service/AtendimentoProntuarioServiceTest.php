<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Exception\AcessoProntuarioNegadoException;
use App\Service\AtendimentoService;
use App\Service\ProntuarioService;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class AtendimentoProntuarioServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios', 'app.Perfis', 'app.Permissoes', 'app.UsuariosPerfis', 'app.PerfisPermissoes',
        'app.Clientes', 'app.Pacientes', 'app.Especialidades', 'app.Profissionais', 'app.Agendas',
        'app.Consultas', 'app.Atendimentos', 'app.Prescricoes', 'app.Prontuarios',
        'app.ProntuariosHistoricos', 'app.Auditorias',
    ];

    public function testAtendimentoNaoCriaDiagnosticoAutomatico(): void
    {
        [$usuario, $paciente, $profissional] = $this->cenario(true);
        $service = new AtendimentoService();
        $atendimento = $service->iniciar([
            'paciente_id' => $paciente->id, 'profissional_id' => $profissional->id,
            'motivo' => 'Avaliação fictícia',
        ], $usuario);
        $service->finalizar($atendimento, [
            'orientacoes' => 'Manter acompanhamento', 'diagnostico' => 'não persistir',
        ], $usuario);

        $this->assertSame('finalizado', $atendimento->status);
        $this->assertFalse($atendimento->has('diagnostico'));
        $this->assertSame(2, TableRegistry::getTableLocator()->get('Auditorias')->find()->count());
    }

    public function testProntuarioVersionaERegistraVisualizacao(): void
    {
        [$usuario, $paciente] = $this->cenario(true);
        $service = new ProntuarioService();
        $service->salvar($paciente->id, ['alergias' => 'Nenhuma conhecida'], $usuario, 'Cadastro inicial');
        $prontuario = $service->salvar($paciente->id, ['alergias' => 'Alergia fictícia'], $usuario, 'Atualização');
        $service->visualizar($paciente->id, $usuario);

        $this->assertSame(2, $prontuario->versao);
        $this->assertSame(1, TableRegistry::getTableLocator()->get('ProntuariosHistoricos')->find()->count());
        $this->assertSame(3, TableRegistry::getTableLocator()->get('Auditorias')->find()->count());
    }

    public function testNegaProntuarioSemPermissao(): void
    {
        [$usuario, $paciente] = $this->cenario(false);
        $this->expectException(AcessoProntuarioNegadoException::class);
        (new ProntuarioService())->salvar($paciente->id, ['observacoes' => 'Restrito'], $usuario);
    }

    /** @return array{0: object, 1: object, 2: object} */
    private function cenario(bool $administrador): array
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $usuario = $usuarios->newEntity([
            'nome' => 'Usuário Clínico', 'email' => uniqid('clinico-') . '@example.test',
            'senha' => 'ValidPassword!123', 'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        if ($administrador) {
            $perfis = TableRegistry::getTableLocator()->get('Perfis');
            $perfil = $perfis->newEntity(['nome' => 'Administrador', 'descricao' => 'Teste', 'ativo' => true]);
            $perfis->saveOrFail($perfil);
            $usuarios->Perfis->link($usuario, [$perfil]);
        }
        $pacientes = TableRegistry::getTableLocator()->get('Pacientes');
        $paciente = $pacientes->newEntity([
            'nome_completo' => 'Paciente Fictício', 'cpf' => '52998224725',
            'data_nascimento' => '1990-01-01', 'ativo' => true,
        ], ['validate' => false]);
        $pacientes->saveOrFail($paciente, ['checkRules' => false]);
        $especialidades = TableRegistry::getTableLocator()->get('Especialidades');
        $especialidade = $especialidades->newEntity(['nome' => uniqid('Clínica '), 'ativo' => true]);
        $especialidades->saveOrFail($especialidade);
        $profissionais = TableRegistry::getTableLocator()->get('Profissionais');
        $profissional = $profissionais->newEntity([
            'especialidade_id' => $especialidade->id, 'nome' => 'Profissional Fictício',
            'cpf' => '11144477735', 'tipo' => 'medico', 'dias_atendimento' => [], 'horarios' => [], 'ativo' => true,
        ], ['validate' => false]);
        $profissionais->saveOrFail($profissional, ['checkRules' => false]);

        return [$usuario, $paciente, $profissional];
    }
}
