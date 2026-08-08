<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Model\Entity\Usuario;
use App\Service\LoginService;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DateTimeImmutable;

final class UsuariosControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = [
        'app.Usuarios',
        'app.Perfis',
        'app.Permissoes',
        'app.UsuariosPerfis',
        'app.PerfisPermissoes',
        'app.TentativasLogin',
        'app.Auditorias',
        'app.Notificacoes',
    ];

    public function testLoginValidoELogoutSaoAuditados(): void
    {
        $usuario = $this->criarUsuario('Administrador');

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/login', ['email' => $usuario->email, 'senha' => 'ValidPassword!123']);
        $this->assertRedirect('/usuarios');

        $tentativas = TableRegistry::getTableLocator()->get('TentativasLogin');
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $this->assertSame(1, $tentativas->find()->where(['sucesso' => true])->count());
        $this->assertSame(1, $auditorias->find()->where(['acao' => 'login'])->count());

        $this->session(['Auth' => $usuario]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/logout');
        $this->assertRedirect('/login');
        $this->assertSame(1, $auditorias->find()->where(['acao' => 'logout'])->count());
    }

    public function testLoginAceitaNovaSessaoSemTokenDeProtecaoDeFormulario(): void
    {
        $usuario = $this->criarUsuario('Administrador');

        $this->enableCsrfToken();
        $this->post('/login', ['email' => $usuario->email, 'senha' => 'ValidPassword!123']);

        $this->assertRedirect('/usuarios');
    }

    public function testLoginInvalidoRegistraTentativa(): void
    {
        $usuario = $this->criarUsuario('Administrador');

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/login', ['email' => $usuario->email, 'senha' => 'senha-incorreta']);
        $this->assertRedirect('/login');

        $tentativas = TableRegistry::getTableLocator()->get('TentativasLogin');
        $this->assertSame(1, $tentativas->find()->where(['sucesso' => false])->count());
    }

    public function testUsuarioBloqueadoAposCincoFalhas(): void
    {
        $usuario = $this->criarUsuario('Administrador');
        $service = new LoginService();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $service->registrarFalha($usuario->email, '127.0.0.1', 'PHPUnit');
        }

        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $atualizado = $usuarios->get($usuario->id);
        $this->assertNotNull($atualizado->bloqueado_ate);
        $this->assertGreaterThan(new DateTimeImmutable(), $atualizado->bloqueado_ate);
        $this->assertNull($usuarios->find('autenticacao')->where(['Usuarios.id' => $usuario->id])->first());
    }

    public function testBloqueioExistenteNaoEProrrogado(): void
    {
        $usuario = $this->criarUsuario('Administrador');
        $usuario->bloqueado_ate = new DateTimeImmutable('+20 minutes');
        TableRegistry::getTableLocator()->get('Usuarios')->saveOrFail($usuario, ['checkRules' => false]);
        $bloqueadoAte = $usuario->bloqueado_ate;

        (new LoginService())->registrarFalha($usuario->email, '127.0.0.1', 'PHPUnit');

        $atualizado = TableRegistry::getTableLocator()->get('Usuarios')->get($usuario->id);
        $this->assertEquals($bloqueadoAte, $atualizado->bloqueado_ate);
        $this->assertSame(0, TableRegistry::getTableLocator()->get('TentativasLogin')->find()->count());
    }

    public function testEsqueciSenhaCriaSolicitacaoParaMaster(): void
    {
        $master = $this->criarUsuario('Administrador');
        $master->email = 'admin@farmaclinic.local';
        TableRegistry::getTableLocator()->get('Usuarios')->saveOrFail($master);
        $usuario = $this->criarUsuario('Consulta');

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/esqueci-senha', ['email' => $usuario->email]);

        $this->assertRedirect('/login');
        $notificacao = TableRegistry::getTableLocator()->get('Notificacoes')->find()->firstOrFail();
        $this->assertSame($master->id, $notificacao->usuario_id);
        $this->assertSame('recuperacao_senha', $notificacao->tipo);
    }

    public function testUsuarioSemPermissaoRecebeForbidden(): void
    {
        $usuario = $this->criarUsuario('Consulta');
        $this->session(['Auth' => $usuario]);
        $this->get('/usuarios');
        $this->assertResponseCode(403);
    }

    private function criarUsuario(string $perfilNome): Usuario
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $perfil = $usuarios->Perfis->newEntity([
            'nome' => $perfilNome,
            'descricao' => 'Perfil de teste',
            'ativo' => true,
        ]);
        $usuarios->Perfis->saveOrFail($perfil);

        $usuario = $usuarios->newEntity([
            'nome' => "Usuário {$perfilNome}",
            'email' => mb_strtolower($perfilNome) . '@example.test',
            'senha' => 'ValidPassword!123',
            'ativo' => true,
        ]);
        $usuarios->saveOrFail($usuario);
        $usuarios->Perfis->link($usuario, [$perfil]);

        return $usuarios->get($usuario->id, contain: ['Perfis.Permissoes']);
    }
}
