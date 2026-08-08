<?php
/** @var \App\View\AppView $this */
$this->assign('title', 'Esqueci a senha');
?>
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 mb-2">Recuperar acesso</h1>
                <p class="text-body-secondary">Informe seu e-mail. O administrador master receberá a solicitação e poderá definir uma nova senha.</p>
                <?= $this->Form->create(null) ?>
                <?= $this->Form->control('email', [
                    'label' => 'E-mail',
                    'type' => 'email',
                    'required' => true,
                    'autocomplete' => 'email',
                    'class' => 'form-control',
                ]) ?>
                <div class="d-grid gap-2 mt-3">
                    <?= $this->Form->button('Solicitar recuperação', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Voltar ao login', '/login', ['class' => 'btn btn-outline-secondary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
