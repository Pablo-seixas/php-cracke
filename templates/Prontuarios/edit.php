<?php $this->assign('title', 'Editar prontuário'); ?><h1 class="h3">Editar prontuário</h1>
<?= $this->Form->create($prontuario) ?><div class="card border-0 shadow-sm mt-3"><div class="card-body">
<?= $this->Form->control('historico_clinico', ['type' => 'textarea', 'rows' => 4, 'label' => 'Histórico clínico']) ?>
<?= $this->Form->control('alergias', ['type' => 'textarea', 'rows' => 3]) ?>
<?= $this->Form->control('medicamentos_uso', ['type' => 'textarea', 'rows' => 3, 'label' => 'Medicamentos em uso']) ?>
<?= $this->Form->control('observacoes', ['type' => 'textarea', 'rows' => 3, 'label' => 'Observações']) ?>
<?= $this->Form->control('motivo_alteracao', ['required' => true, 'label' => 'Motivo da alteração']) ?>
</div><div class="card-footer bg-white"><?= $this->Form->button('Salvar nova versão', ['class' => 'btn btn-primary']) ?></div></div><?= $this->Form->end() ?>
