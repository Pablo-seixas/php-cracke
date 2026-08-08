<?php $this->assign('title', 'Novo atendimento'); ?>
<h1 class="h3">Novo atendimento</h1>
<?= $this->Form->create() ?><div class="card border-0 shadow-sm mt-3"><div class="card-body row g-3">
<div class="col-md-6"><?= $this->Form->control('paciente_id', ['options' => $pacientes, 'label' => 'Paciente']) ?></div>
<div class="col-md-6"><?= $this->Form->control('profissional_id', ['options' => $profissionais, 'label' => 'Profissional']) ?></div>
<div class="col-12"><?= $this->Form->control('motivo', ['type' => 'textarea', 'rows' => 2]) ?></div>
<div class="col-12"><?= $this->Form->control('sintomas', ['type' => 'textarea', 'rows' => 2]) ?></div>
</div><div class="card-footer bg-white"><?= $this->Form->button('Iniciar', ['class' => 'btn btn-primary']) ?></div></div><?= $this->Form->end() ?>
