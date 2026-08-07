<?php
/** @var \App\View\AppView $this */
/** @var iterable<\App\Model\Entity\Perfi> $perfis */
$this->assign('title', 'Perfis e permissões');
?>
<h1 class="h3 mb-1">Perfis e permissões</h1>
<p class="text-body-secondary mb-4">Aplique o princípio do menor privilégio.</p>
<div class="row g-3">
    <?php foreach ($perfis as $perfil): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="d-flex justify-content-between"><h2 class="h5"><?= h($perfil->nome) ?></h2>
                    <span class="badge text-bg-<?= $perfil->ativo ? 'success' : 'secondary' ?>"><?= $perfil->ativo ? 'Ativo' : 'Inativo' ?></span>
                </div>
                <p class="small text-body-secondary"><?= count($perfil->permissoes ?? []) ?> permissões</p>
                <?= $this->Html->link('Configurar', ['action' => 'edit', $perfil->id], ['class' => 'btn btn-outline-primary']) ?>
            </div></div>
        </div>
    <?php endforeach; ?>
</div>
