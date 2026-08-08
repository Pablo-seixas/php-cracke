<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Medicamento $medicamento */
$this->assign('title', $medicamento->nome_comercial);
?>
<div class="d-flex justify-content-between mb-4"><div><h1 class="h3 mb-1"><?= h($medicamento->nome_comercial) ?></h1><p class="text-body-secondary"><?= h($medicamento->principio_ativo) ?></p></div><div class="d-flex gap-2"><?= $this->Html->link('Editar', ['action' => 'edit', $medicamento->id], ['class' => 'btn btn-primary']) ?><?= $this->Form->postLink('Inativar', ['action' => 'inativar', $medicamento->id], ['class' => 'btn btn-outline-danger', 'confirm' => 'Confirma a inativação?']) ?></div></div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-body-secondary">Estoque vendável</small><div class="display-6 fw-semibold"><?= number_format($medicamento->estoqueAtual(), 3, ',', '.') ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-body-secondary">Preço de venda</small><div class="display-6 fw-semibold">R$ <?= number_format((float)$medicamento->preco_venda, 2, ',', '.') ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-body-secondary">Fabricante</small><div class="h4 mt-2"><?= h($medicamento->fabricante->nome ?? '-') ?></div></div></div></div>
</div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-transparent"><h2 class="h5 mb-0">Lotes por validade (FEFO)</h2></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Lote</th><th>Validade</th><th>Disponível</th><th>Status</th></tr></thead><tbody><?php foreach ($medicamento->lotes ?? [] as $lote): ?><tr><td><?= h($lote->numero_lote) ?></td><td><?= h($lote->data_validade?->format('d/m/Y')) ?></td><td><?= h($lote->quantidade_disponivel) ?></td><td><?= $lote->bloqueado ? '<span class="badge text-bg-danger">Bloqueado</span>' : '<span class="badge text-bg-success">Disponível</span>' ?></td></tr><?php endforeach; ?></tbody></table></div></div>
