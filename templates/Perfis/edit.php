<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Perfi $perfil */
/** @var \Cake\Collection\CollectionInterface|array $permissoes */
$this->assign('title', 'Editar perfil');
?>
<h1 class="h3 mb-4">Editar perfil: <?= h($perfil->nome) ?></h1>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <?= $this->Form->create($perfil) ?>
    <?= $this->Form->control('nome', ['class' => 'form-control']) ?>
    <?= $this->Form->control('descricao', ['class' => 'form-control']) ?>
    <?= $this->Form->control('permissoes._ids', [
        'options' => $permissoes,
        'class' => 'form-select',
        'multiple' => true,
        'label' => 'Permissões',
    ]) ?>
    <?= $this->Form->control('ativo', ['class' => 'form-check-input']) ?>
    <div class="d-flex gap-2 mt-4">
        <?= $this->Form->button('Salvar', ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
</div></div>
