<?php
/** @var \App\View\AppView $this */
/** @var iterable<\App\Model\Entity\Usuario> $usuarios */
$this->assign('title', 'Usuários');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Usuários</h1>
        <p class="text-body-secondary mb-0">Gerencie acessos, perfis e bloqueios.</p>
    </div>
    <?= $this->Html->link('Novo usuário', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Perfis</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= h($usuario->nome) ?></td>
                    <td><?= h($usuario->email) ?></td>
                    <td><?= h(implode(', ', array_map(fn($perfil) => $perfil->nome, $usuario->perfis ?? []))) ?></td>
                    <td><span class="badge text-bg-<?= $usuario->ativo ? 'success' : 'secondary' ?>"><?= $usuario->ativo ? 'Ativo' : 'Inativo' ?></span></td>
                    <td class="text-end">
                        <?= $this->Html->link('Editar', ['action' => 'edit', $usuario->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?php if ($usuario->ativo): ?>
                            <?= $this->Form->postLink('Inativar', ['action' => 'inativar', $usuario->id], [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'confirm' => 'Confirma a inativação deste usuário?',
                            ]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<nav class="mt-3" aria-label="Paginação">
    <ul class="pagination">
        <?= $this->Paginator->prev('Anterior') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('Próxima') ?>
    </ul>
</nav>
