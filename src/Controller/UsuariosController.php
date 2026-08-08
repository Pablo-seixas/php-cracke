<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Usuario;
use App\Model\Table\UsuariosTable;
use App\Service\LoginService;
use App\Service\RecuperacaoSenhaService;
use Authentication\IdentityInterface as AuthenticationIdentity;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use DateTimeImmutable;

final class UsuariosController extends AppController
{
    protected UsuariosTable $Usuarios;

    /**
     * Load the users table.
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Usuarios = $this->fetchTable('Usuarios');
    }

    /**
     * Allow unauthenticated access to the login action.
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'esqueciSenha']);
    }

    /**
     * Authenticate a user and register the attempt.
     */
    public function login(): ?Response
    {
        $this->Authorization->skipAuthorization();
        if (!$this->request->is('post')) {
            return null;
        }

        $service = new LoginService();
        $result = $this->Authentication->getResult();
        $ip = $this->request->clientIp();
        $userAgent = $this->request->getHeaderLine('User-Agent') ?: null;
        if (!$result->isValid()) {
            $service->registrarFalha((string)$this->request->getData('email'), $ip, $userAgent);
            $this->Flash->error('E-mail ou senha inválidos, ou usuário temporariamente bloqueado.');

            return $this->redirect('/login');
        }

        $identity = $this->Authentication->getIdentity();
        $usuario = $this->extrairUsuario($identity);
        if ($usuario !== null) {
            $service->registrarSucesso($usuario, $ip, $userAgent);
        }

        $redirect = $this->request->getQuery('redirect');

        return $this->redirect(is_string($redirect) && str_starts_with($redirect, '/') ? $redirect : '/usuarios');
    }

    /**
     * End the authenticated session and audit the event.
     */
    public function logout(): Response
    {
        $this->request->allowMethod(['post']);
        $this->Authorization->skipAuthorization();
        $usuario = $this->extrairUsuario($this->Authentication->getIdentity());
        if ($usuario !== null) {
            (new LoginService())->registrarLogout(
                $usuario,
                $this->request->clientIp(),
                $this->request->getHeaderLine('User-Agent') ?: null,
            );
        }
        $this->Authentication->logout();

        return $this->redirect('/login');
    }

    /** Register a password recovery request without revealing account existence. */
    public function esqueciSenha(): ?Response
    {
        $this->Authorization->skipAuthorization();
        if ($this->request->is('post')) {
            (new RecuperacaoSenhaService())->solicitar((string)$this->request->getData('email'));
            $this->Flash->success(
                'Se o e-mail estiver cadastrado, o administrador master receberá a solicitação.',
            );

            return $this->redirect('/login');
        }

        return null;
    }

    /**
     * List users when the current identity has management permission.
     */
    public function index(): void
    {
        $this->Authorization->authorize($this->Usuarios, 'index');
        $query = $this->Usuarios->find()->contain(['Perfis'])->orderBy(['Usuarios.nome' => 'ASC']);
        $usuarioAtual = $this->extrairUsuario($this->Authentication->getIdentity());
        $recuperacoes = [];
        if ($usuarioAtual?->email === 'admin@farmaclinic.local') {
            $recuperacoes = $this->fetchTable('Notificacoes')->find()
                ->where(['usuario_id' => $usuarioAtual->id, 'tipo' => 'recuperacao_senha', 'lida_em IS' => null])
                ->orderByDesc('created')
                ->all();
        }
        $this->set('usuarios', $this->paginate($query));
        $this->set(compact('recuperacoes', 'usuarioAtual'));
    }

    /**
     * Create a user and assign profiles.
     */
    public function add(): ?Response
    {
        $this->Authorization->authorize($this->Usuarios, 'add');
        $usuario = $this->Usuarios->newEmptyEntity();
        if ($this->request->is('post')) {
            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData(), [
                'associated' => ['Perfis'],
            ]);
            if ($this->Usuarios->save($usuario)) {
                $this->Flash->success('Usuário criado com sucesso.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Não foi possível criar o usuário.');
        }
        $this->set('perfis', $this->Usuarios->Perfis->find('list')->where(['ativo' => true])->all());
        $this->set(compact('usuario'));

        return null;
    }

    /**
     * Edit a user and profile assignments.
     */
    public function edit(int $id): ?Response
    {
        $usuario = $this->Usuarios->get($id, contain: ['Perfis']);
        $this->Authorization->authorize($usuario, 'edit');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (($data['senha'] ?? '') === '') {
                unset($data['senha']);
            }
            $usuario = $this->Usuarios->patchEntity($usuario, $data, ['associated' => ['Perfis']]);
            if ($this->Usuarios->save($usuario)) {
                $this->Flash->success('Usuário atualizado com sucesso.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Não foi possível atualizar o usuário.');
        }
        $this->set('perfis', $this->Usuarios->Perfis->find('list')->where(['ativo' => true])->all());
        $this->set(compact('usuario'));

        return null;
    }

    /**
     * Soft-delete a user account.
     */
    public function inativar(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $usuario = $this->Usuarios->get($id);
        $this->Authorization->authorize($usuario, 'inativar');
        $usuario->ativo = false;
        $usuario->deleted = new DateTimeImmutable();
        $this->Usuarios->saveOrFail($usuario);
        $this->Flash->success('Usuário inativado com sucesso.');

        return $this->redirect(['action' => 'index']);
    }

    /** Unlock an account and discard its recent failed attempts. */
    public function desbloquear(int $id): Response
    {
        $this->request->allowMethod(['post']);
        $usuario = $this->Usuarios->get($id);
        $this->Authorization->authorize($usuario, 'desbloquear');
        $usuario->bloqueado_ate = null;
        $this->Usuarios->saveOrFail($usuario, ['checkRules' => false]);
        $this->Usuarios->TentativasLogin->deleteAll([
            'OR' => ['usuario_id' => $usuario->id, 'identificador' => $usuario->email],
            'sucesso' => false,
        ]);
        $this->Flash->success('Usuário desbloqueado com sucesso.');

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Extract the domain user from the authentication identity.
     */
    private function extrairUsuario(?AuthenticationIdentity $identity): ?Usuario
    {
        $data = $identity?->getOriginalData();

        return $data instanceof Usuario ? $data : null;
    }
}
