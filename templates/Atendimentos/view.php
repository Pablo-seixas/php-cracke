<?php $this->assign('title', 'Atendimento'); ?>
<div class="d-flex justify-content-between"><h1 class="h3">Atendimento</h1><?= $this->Html->link('Prontuário', ['controller' => 'Prontuarios', 'action' => 'view', $atendimento->paciente_id], ['class' => 'btn btn-outline-primary']) ?></div>
<div class="card border-0 shadow-sm mt-3"><div class="card-body">
<h2 class="h5"><?= h($atendimento->paciente->nome_completo) ?></h2><p>Profissional: <?= h($atendimento->profissional->nome) ?></p>
<dl class="row"><dt class="col-sm-3">Motivo</dt><dd class="col-sm-9"><?= h($atendimento->motivo ?: '-') ?></dd><dt class="col-sm-3">Sintomas</dt><dd class="col-sm-9"><?= h($atendimento->sintomas ?: '-') ?></dd><dt class="col-sm-3">Orientações</dt><dd class="col-sm-9"><?= h($atendimento->orientacoes ?: '-') ?></dd></dl>
</div></div>
