<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\PerfilService;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class PerfilServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Usuarios',
        'app.Perfis',
        'app.Permissoes',
        'app.UsuariosPerfis',
        'app.PerfisPermissoes',
        'app.Auditorias',
    ];

    public function testAtualizaPermissoesComAuditoria(): void
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $perfis = TableRegistry::getTableLocator()->get('Perfis');
        $permissoes = TableRegistry::getTableLocator()->get('Permissoes');
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');

        $responsavel = $usuarios->newEntity([
            'nome' => 'Administrador de teste',
            'email' => 'admin-service@example.test',
            'senha' => 'ValidPassword!123',
            'ativo' => true,
        ]);
        $usuarios->saveOrFail($responsavel);
        $perfil = $perfis->newEntity(['nome' => 'Operacional', 'ativo' => true]);
        $perfis->saveOrFail($perfil);
        $permissao = $permissoes->newEntity([
            'codigo' => 'estoque.visualizar',
            'descricao' => 'Visualizar estoque',
        ]);
        $permissoes->saveOrFail($permissao);

        $atualizado = (new PerfilService($perfis))->atualizar(
            $perfil,
            ['permissoes' => ['_ids' => [$permissao->id]]],
            $responsavel,
            '127.0.0.1',
            'PHPUnit',
        );

        $this->assertCount(1, $atualizado->permissoes);
        $this->assertSame('estoque.visualizar', $atualizado->permissoes[0]->codigo);
        $auditoria = $auditorias->find()->where(['acao' => 'alteracao_permissoes'])->firstOrFail();
        $this->assertSame($perfil->id, $auditoria->registro_id);
        $this->assertSame('127.0.0.1', $auditoria->endereco_ip);
    }
}
