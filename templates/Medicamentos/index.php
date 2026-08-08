<?php
/** @var \App\View\AppView $this */
/** @var iterable<\App\Model\Entity\Medicamento> $medicamentos */
/** @var string $busca */
$this->assign('title', 'Medicamentos');
$badges = ['sem_estoque' => 'danger', 'critico' => 'warning', 'normal' => 'success', 'elevado' => 'info'];
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h3 mb-1">Medicamentos</h1><p class="text-body-secondary mb-0">Catálogo, estoque e validade em um só lugar.</p></div>
    <div class="d-flex gap-2">
        <?= $this->Html->link('Lotes e validade', ['controller' => 'Lotes'], ['class' => 'btn btn-outline-primary']) ?>
        <?= $this->Html->link('Novo medicamento', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<div class="card border-0 shadow-sm mb-4"><div class="card-body">
    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row g-2']) ?>
    <div class="col-md-8"><?= $this->Form->control('busca', ['value' => $busca, 'label' => false, 'placeholder' => 'Nome, princípio ativo, código ou código de barras', 'class' => 'form-control']) ?></div>
    <div class="col-md-2"><?= $this->Form->control('controlado', ['type' => 'checkbox', 'value' => '1', 'label' => 'Controlados', 'class' => 'form-check-input']) ?></div>
    <div class="col-md-2"><?= $this->Form->button('Pesquisar', ['class' => 'btn btn-dark w-100']) ?></div>
    <?= $this->Form->end() ?>
</div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead><tr><th>Medicamento</th><th>Categoria</th><th>Estoque</th><th>Venda</th><th>Alertas</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($medicamentos as $medicamento): $status = $medicamento->statusEstoque(); ?>
        <tr>
            <td><strong><?= h($medicamento->nome_comercial) ?></strong><br><small class="text-body-secondary"><?= h($medicamento->principio_ativo) ?> · <?= h($medicamento->codigo_interno) ?></small></td>
            <td><?= h($medicamento->categoria->nome ?? '-') ?></td>
            <td><span class="badge text-bg-<?= $badges[$status] ?>"><?= number_format($medicamento->estoqueAtual(), 3, ',', '.') ?></span></td>
            <td>R$ <?= number_format((float)$medicamento->preco_venda, 2, ',', '.') ?></td>
            <td><?php if ($medicamento->controlado): ?><span class="badge text-bg-danger">Controlado</span><?php endif; ?> <?php if ($medicamento->necessita_receita): ?><span class="badge text-bg-warning">Receita</span><?php endif; ?></td>
            <td class="text-end"><?= $this->Html->link('Detalhes', ['action' => 'view', $medicamento->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div></div>
<nav class="mt-3"><ul class="pagination"><?= $this->Paginator->prev('Anterior') ?><?= $this->Paginator->numbers() ?><?= $this->Paginator->next('Próxima') ?></ul></nav>
