<?php $this->assign('title', 'Novo lote'); ?>
<h1 class="h3 mb-4">Receber novo lote</h1><div class="card border-0 shadow-sm"><div class="card-body p-4">
<?= $this->Form->create($lote) ?><div class="row g-3">
<div class="col-md-6"><?= $this->Form->control('medicamento_id', ['options' => $medicamentos, 'class' => 'form-select']) ?></div><div class="col-md-6"><?= $this->Form->control('fornecedor_id', ['options' => $fornecedores, 'empty' => true, 'class' => 'form-select']) ?></div>
<div class="col-md-4"><?= $this->Form->control('numero_lote', ['class' => 'form-control']) ?></div><div class="col-md-4"><?= $this->Form->control('data_fabricacao', ['class' => 'form-control']) ?></div><div class="col-md-4"><?= $this->Form->control('data_validade', ['class' => 'form-control']) ?></div>
<div class="col-md-4"><?= $this->Form->control('quantidade_recebida', ['class' => 'form-control']) ?></div><div class="col-md-4"><?= $this->Form->control('preco_custo', ['class' => 'form-control']) ?></div><div class="col-md-4"><?= $this->Form->control('nota_fiscal', ['class' => 'form-control']) ?></div>
</div><div class="d-flex gap-2 mt-4"><?= $this->Form->button('Cadastrar lote', ['class' => 'btn btn-primary']) ?><?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?></div><?= $this->Form->end() ?>
</div></div>
