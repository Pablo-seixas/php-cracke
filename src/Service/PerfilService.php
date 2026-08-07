<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Perfi;
use App\Model\Entity\Usuario;
use App\Model\Table\PerfisTable;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

final class PerfilService
{
    /**
     * Constructor.
     */
    public function __construct(private readonly PerfisTable $perfis)
    {
    }

    /**
     * Update a profile and its permission links in one transaction.
     *
     * @param array<string, mixed> $data Validated request data.
     */
    public function atualizar(
        Perfi $perfil,
        array $data,
        Usuario $responsavel,
        ?string $ip,
        ?string $userAgent,
    ): Perfi {
        $antes = [
            'nome' => $perfil->nome,
            'ativo' => $perfil->ativo,
            'permissoes' => array_map(fn($item): int => $item->id, $perfil->permissoes ?? []),
        ];

        return ConnectionManager::get('default')->transactional(function () use (
            $perfil,
            $data,
            $responsavel,
            $ip,
            $userAgent,
            $antes,
        ): Perfi {
            $perfil = $this->perfis->patchEntity($perfil, $data, ['associated' => ['Permissoes']]);
            $this->perfis->saveOrFail($perfil);
            $depois = [
                'nome' => $perfil->nome,
                'ativo' => $perfil->ativo,
                'permissoes' => array_map(fn($item): int => $item->id, $perfil->permissoes ?? []),
            ];

            $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
            $auditorias->saveOrFail($auditorias->newEntity([
                'usuario_id' => $responsavel->id,
                'acao' => 'alteracao_permissoes',
                'modulo' => 'perfis',
                'registro_id' => $perfil->id,
                'dados_anteriores' => $antes,
                'dados_posteriores' => $depois,
                'endereco_ip' => filter_var($ip, FILTER_VALIDATE_IP) !== false ? $ip : null,
                'user_agent' => $userAgent,
            ]));

            return $perfil;
        });
    }
}
