<?php
// Inclui o header para carregar o CSS e o tema global
include_once __DIR__ . '/../_header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow-sm p-5 text-center" style="max-width: 600px; width: 100%;">
        <h2 class="mb-4">
            <?= htmlspecialchars($msg) ?>
        </h2>

        <a href="<?= BASE_URL ?>/transactions" class="btn btn-primary">
            ← Voltar para Transações
        </a>
    </div>
</div>

<?php
// Inclui o footer para fechar o HTML e carregar os scripts do tema
include_once __DIR__ . '/../_footer.php';
?>
