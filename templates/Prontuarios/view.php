<?php $this->assign('title', 'Prontuário'); ?>
<div class="d-flex justify-content-between"><h1 class="h3">Prontuário de <?= h($prontuario->paciente->nome_completo) ?></h1><?= $this->Html->link('Editar', ['action' => 'edit', $prontuario->paciente_id], ['class' => 'btn btn-primary']) ?></div>
<div class="alert alert-info mt-3">Acesso registrado para fins de segurança e LGPD. Versão <?= h($prontuario->versao) ?>.</div>
<div class="card border-0 shadow-sm"><div class="card-body"><dl class="row">
<dt class="col-sm-3">Histórico clínico</dt><dd class="col-sm-9"><?= nl2br(h($prontuario->historico_clinico ?: '-')) ?></dd>
<dt class="col-sm-3">Alergias</dt><dd class="col-sm-9"><?= nl2br(h($prontuario->alergias ?: '-')) ?></dd>
<dt class="col-sm-3">Medicamentos em uso</dt><dd class="col-sm-9"><?= nl2br(h($prontuario->medicamentos_uso ?: '-')) ?></dd>
<dt class="col-sm-3">Observações</dt><dd class="col-sm-9"><?= nl2br(h($prontuario->observacoes ?: '-')) ?></dd>
</dl></div></div>
