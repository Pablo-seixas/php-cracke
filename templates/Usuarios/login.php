<?php
/** @var \App\View\AppView $this */
$this->assign('title', 'Entrar');
?>
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-7 col-lg-5 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 mb-2">FarmaClinic Manager</h1>
                <p class="text-body-secondary mb-4">Acesse o ambiente de gestão.</p>
                <?= $this->Form->create(null) ?>
                <?= $this->Form->control('email', [
                    'label' => 'E-mail',
                    'type' => 'email',
                    'required' => true,
                    'autocomplete' => 'username',
                    'class' => 'form-control',
                ]) ?>
                <?= $this->Form->control('senha', [
                    'label' => 'Senha',
                    'type' => 'password',
                    'required' => true,
                    'autocomplete' => 'current-password',
                    'class' => 'form-control',
                ]) ?>
                <?= $this->Form->button('Entrar', ['class' => 'btn btn-primary w-100 mt-3']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
