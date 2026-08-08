<?php
/** @var \App\View\AppView $this */
/** @var iterable<\App\Model\Entity\Lote> $lotes */
$this->assign('title', 'Lotes e validade');
?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Lotes e validade</h1><p class="text-body-secondary mb-0">Ordem FEFO: primeiro a validade mais próxima.</p></div><?= $this->Html->link('Novo lote', ['action' => 'add'], ['class' => 'btn btn-primary']) ?></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Validade</th><th>Medicamento</th><th>Lote</th><th>Disponível</th><th>Status</th><th>Ação</th></tr></thead><tbody>
<?php foreach ($lotes as $lote): $vencido = $lote->data_validade < \Cake\I18n\Date::today(); ?>
<tr><td class="<?= $vencido ? 'text-danger fw-bold' : '' ?>"><?= h($lote->data_validade?->format('d/m/Y')) ?></td><td><?= h($lote->medicamento->nome_comercial ?? '-') ?></td><td><?= h($lote->numero_lote) ?></td><td><?= h($lote->quantidade_disponivel) ?></td><td><?php if ($lote->recolhido): ?><span class="badge text-bg-dark">Recolhido</span><?php elseif ($lote->bloqueado): ?><span class="badge text-bg-danger">Bloqueado</span><?php elseif ($vencido): ?><span class="badge text-bg-danger">Vencido</span><?php else: ?><span class="badge text-bg-success">Disponível</span><?php endif; ?></td><td><?= $this->Form->postLink('Bloquear', ['action' => 'bloquear', $lote->id], ['data' => ['motivo' => 'Bloqueio operacional', 'tipo' => 'bloquear'], 'class' => 'btn btn-sm btn-outline-danger', 'confirm' => 'Bloquear este lote?']) ?></td></tr>
<?php endforeach; ?></tbody></table></div></div>
