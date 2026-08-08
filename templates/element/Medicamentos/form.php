<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Medicamento $medicamento */
?>
<?= $this->Form->create($medicamento) ?>
<div class="row g-3">
    <div class="col-md-4"><?= $this->Form->control('codigo_interno', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('codigo_barras', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('registro_sanitario', ['class' => 'form-control']) ?></div>
    <div class="col-md-6"><?= $this->Form->control('nome_comercial', ['class' => 'form-control']) ?></div>
    <div class="col-md-6"><?= $this->Form->control('principio_ativo', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('categoria_id', ['options' => $categorias, 'class' => 'form-select']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('fabricante_id', ['options' => $fabricantes, 'class' => 'form-select']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('forma_farmaceutica', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('concentracao', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('apresentacao', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('unidade_medida', ['class' => 'form-control']) ?></div>
    <div class="col-md-3"><?= $this->Form->control('preco_custo', ['class' => 'form-control']) ?></div>
    <div class="col-md-3"><?= $this->Form->control('preco_venda', ['class' => 'form-control']) ?></div>
    <div class="col-md-3"><?= $this->Form->control('estoque_minimo', ['class' => 'form-control']) ?></div>
    <div class="col-md-3"><?= $this->Form->control('estoque_maximo', ['class' => 'form-control']) ?></div>
    <div class="col-md-4"><?= $this->Form->control('localizacao', ['class' => 'form-control']) ?></div>
    <div class="col-md-8 d-flex flex-wrap gap-4 align-items-end pb-2">
        <?= $this->Form->control('necessita_receita', ['class' => 'form-check-input']) ?>
        <?= $this->Form->control('controlado', ['class' => 'form-check-input']) ?>
        <?= $this->Form->control('refrigerado', ['class' => 'form-check-input']) ?>
        <?= $this->Form->control('ativo', ['class' => 'form-check-input']) ?>
    </div>
    <div class="col-md-4"><?= $this->Form->control('tipo_receita', ['class' => 'form-control']) ?></div>
</div>
<div class="d-flex gap-2 mt-4"><?= $this->Form->button('Salvar', ['class' => 'btn btn-primary']) ?><?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?></div>
<?= $this->Form->end() ?>
