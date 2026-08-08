<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;

final class RecuperacaoSenhaService
{
    /** Create an internal password recovery request for the master administrator. */
    public function solicitar(string $email): void
    {
        $email = mb_strtolower(trim($email));
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $solicitante = $usuarios->find()->where([
            'email' => $email,
            'ativo' => true,
            'deleted IS' => null,
        ])->first();
        if ($solicitante === null) {
            return;
        }

        $master = $usuarios->find()->where(['email' => 'admin@farmaclinic.local'])->first();
        if ($master === null) {
            return;
        }

        $notificacoes = TableRegistry::getTableLocator()->get('Notificacoes');
        $notificacoes->saveOrFail($notificacoes->newEntity([
            'usuario_id' => $master->id,
            'tipo' => 'recuperacao_senha',
            'titulo' => 'Solicitação de recuperação de senha',
            'mensagem' => sprintf('%s solicitou uma nova senha.', $solicitante->email),
            'dados' => ['usuario_id' => $solicitante->id, 'email' => $solicitante->email],
        ]));
    }
}
