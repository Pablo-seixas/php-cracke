<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Usuario $usuario */
/** @var \Cake\Collection\CollectionInterface|array $perfis */
?>
<?= $this->Form->create($usuario) ?>
<div class="row g-3">
    <div class="col-md-6"><?= $this->Form->control('nome', ['class' => 'form-control']) ?></div>
    <div class="col-md-6"><?= $this->Form->control('email', ['class' => 'form-control']) ?></div>
    <div class="col-md-6"><?= $this->Form->control('senha', [
        'type' => 'password',
        'required' => $usuario->isNew(),
        'value' => '',
        'class' => 'form-control',
        'help' => 'Mínimo de 12 caracteres.',
    ]) ?></div>
    <div class="col-md-6"><?= $this->Form->control('perfis._ids', [
        'options' => $perfis,
        'class' => 'form-select',
        'label' => 'Perfis',
    ]) ?></div>
    <div class="col-12"><?= $this->Form->control('ativo', ['class' => 'form-check-input']) ?></div>
</div>
<div class="d-flex gap-2 mt-4">
    <?= $this->Form->button('Salvar', ['class' => 'btn btn-primary']) ?>
    <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?= $this->Form->end() ?>
