<?php $this->assign('title', 'Atendimentos'); ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3">Atendimentos</h1>
    <?= $this->Html->link('Novo atendimento', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
</div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>Início</th><th>Paciente</th><th>Profissional</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($atendimentos as $item): ?><tr>
<td><?= h($item->iniciado_em) ?></td><td><?= h($item->paciente->nome_completo) ?></td>
<td><?= h($item->profissional->nome) ?></td><td><span class="badge text-bg-secondary"><?= h($item->status) ?></span></td>
<td><?= $this->Html->link('Abrir', ['action' => 'view', $item->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
</tr><?php endforeach; ?></tbody></table></div></div>
