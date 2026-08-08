<?= $this->Form->create($especialidade) ?>
<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <?= $this->Form->control('nome', ['label' => 'Nome']) ?>
        <?= $this->Form->control('descricao', ['label' => 'Descrição', 'rows' => 3]) ?>
        <?= $this->Form->control('ativo', ['label' => 'Ativa', 'type' => 'checkbox']) ?>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <?= $this->Form->button('Salvar', ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
</div>
<?= $this->Form->end() ?>
