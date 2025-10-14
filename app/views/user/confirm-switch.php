<?php
// app/views/user/confirm-switch.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Troca de Conta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="form-container" style="max-width: 450px; margin-top: 80px;">
        <h1>Confirmar Acesso</h1>
        <p>
            Por segurança, confirme a senha da conta **<?= htmlspecialchars($targetName) ?>**
            para realizar a troca de perfil.
        </p>

        <?php if (isset($error)): ?>
            <p style="color:red; font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/user/do-switch">
            <input type="hidden" name="id" value="<?= htmlspecialchars($targetUserId) ?>">

            <label for="password">Senha da Conta:</label>
            <input type="password" id="password" name="password" required placeholder="********">

            <button type="submit" class="btn primary" style="width: 100%;">Confirmar e Trocar</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;"><a href="<?= BASE_URL ?>/user/switch-accounts">Cancelar Troca</a></p>
    </div>
</body>
</html>