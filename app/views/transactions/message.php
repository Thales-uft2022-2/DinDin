<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
<script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="message">
    <h2><?= htmlspecialchars($msg) ?></h2>
    <p><a href="<?= BASE_URL ?>/transactions/index">Voltar</a></p>
</div>
