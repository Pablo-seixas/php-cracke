<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Usuario;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

final class LoginService
{
    private const MAX_ATTEMPTS = 5;
    private const ATTEMPT_WINDOW_MINUTES = 15;
    private const LOCK_MINUTES = 30;

    /**
     * Register an invalid login and lock a known user when the threshold is reached.
     */
    public function registrarFalha(string $email, ?string $ip, ?string $userAgent): void
    {
        $email = mb_strtolower(trim($email));
        $ip = $this->normalizarIp($ip);
        $connection = ConnectionManager::get('default');
        $connection->transactional(function () use ($email, $ip, $userAgent): void {
            $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
            $tentativas = TableRegistry::getTableLocator()->get('TentativasLogin');
            $usuario = $usuarios->find()->where(['email' => $email])->first();

            // Never renew an existing lock on every rejected form submission.
            if (
                $usuario instanceof Usuario && $usuario->bloqueado_ate !== null
                && $usuario->bloqueado_ate > DateTime::now()
            ) {
                return;
            }

            $tentativa = $tentativas->newEntity([
                'usuario_id' => $usuario?->id,
                'identificador' => $email,
                'sucesso' => false,
                'endereco_ip' => $ip,
                'user_agent' => $userAgent,
                'motivo' => 'credenciais_invalidas',
            ]);
            $tentativas->saveOrFail($tentativa);

            if (!$usuario instanceof Usuario) {
                return;
            }

            $desde = DateTime::now()->subMinutes(self::ATTEMPT_WINDOW_MINUTES);
            $falhas = $tentativas->find()
                ->where([
                    'usuario_id' => $usuario->id,
                    'sucesso' => false,
                    'created >=' => $desde,
                ])
                ->count();
            if ($falhas >= self::MAX_ATTEMPTS) {
                $usuario->bloqueado_ate = DateTime::now()->addMinutes(self::LOCK_MINUTES);
                $usuarios->saveOrFail($usuario, ['checkRules' => false]);
            }
        });
    }

    /**
     * Register a successful login and update the user's access timestamp.
     */
    public function registrarSucesso(Usuario $usuario, ?string $ip, ?string $userAgent): void
    {
        $this->registrarEvento($usuario, 'login', $this->normalizarIp($ip), $userAgent);
    }

    /**
     * Register a logout audit event.
     */
    public function registrarLogout(Usuario $usuario, ?string $ip, ?string $userAgent): void
    {
        $this->registrarAuditoria($usuario, 'logout', $this->normalizarIp($ip), $userAgent);
    }

    /** Register a successful authentication event transactionally. */
    private function registrarEvento(Usuario $usuario, string $acao, ?string $ip, ?string $userAgent): void
    {
        $connection = ConnectionManager::get('default');
        $connection->transactional(function () use ($usuario, $acao, $ip, $userAgent): void {
            $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
            $tentativas = TableRegistry::getTableLocator()->get('TentativasLogin');

            $usuario->ultimo_login = DateTime::now();
            $usuario->bloqueado_ate = null;
            $usuarios->saveOrFail($usuario, ['checkRules' => false]);
            $tentativas->saveOrFail($tentativas->newEntity([
                'usuario_id' => $usuario->id,
                'identificador' => $usuario->email,
                'sucesso' => true,
                'endereco_ip' => $ip,
                'user_agent' => $userAgent,
                'motivo' => $acao,
            ]));
            $this->registrarAuditoria($usuario, $acao, $ip, $userAgent);
        });
    }

    /** Persist an authentication audit record. */
    private function registrarAuditoria(Usuario $usuario, string $acao, ?string $ip, ?string $userAgent): void
    {
        $auditorias = TableRegistry::getTableLocator()->get('Auditorias');
        $auditorias->saveOrFail($auditorias->newEntity([
            'usuario_id' => $usuario->id,
            'acao' => $acao,
            'modulo' => 'autenticacao',
            'registro_id' => $usuario->id,
            'endereco_ip' => $ip,
            'user_agent' => $userAgent,
        ]));
    }

    /** Convert invalid or empty addresses to null for PostgreSQL INET. */
    private function normalizarIp(?string $ip): ?string
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false ? $ip : null;
    }
}
