<?php
declare(strict_types=1);

/** @var \App\View\AppView $this */

use App\Model\Entity\Usuario;

$identity = $this->getRequest()->getAttribute('identity');
$identityData = $identity?->getOriginalData();
$usuarioAtual = $identityData instanceof Usuario ? $identityData->nome : null;
?>
<!doctype html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($this->fetch('title') ?: 'FarmaClinic Manager') ?> · FarmaClinic</title>
    <?= $this->Html->meta('icon') ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body class="bg-body-tertiary min-vh-100">
    <nav class="navbar navbar-expand-lg bg-primary navbar-dark shadow-sm">
        <div class="container">
            <?= $this->Html->link('FarmaClinic Manager', '/', ['class' => 'navbar-brand fw-semibold']) ?>
            <?php if ($usuarioAtual !== null): ?>
                <div class="d-flex align-items-center gap-3 text-white">
                    <?= $this->Html->link('Medicamentos', '/medicamentos', ['class' => 'text-white text-decoration-none']) ?>
                    <?= $this->Html->link('Lotes', '/lotes', ['class' => 'text-white text-decoration-none']) ?>
                    <?= $this->Html->link('Usuários', '/usuarios', ['class' => 'text-white text-decoration-none']) ?>
                    <span><?= h($usuarioAtual) ?></span>
                    <?= $this->Form->postLink('Sair', '/logout', ['class' => 'btn btn-sm btn-outline-light']) ?>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container py-4">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <?= $this->fetch('script') ?>
</body>
</html>
