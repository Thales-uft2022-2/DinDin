<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8"> 
    <title>Redefinir Senha - DinDin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css"> 
</head>
<body>
    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="form-container">
        <h1>Crie uma Nova Senha</h1>
        <form method="POST" action="<?= BASE_URL ?>/auth/update-password">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <label for="password">Nova Senha (mín. 8 caracteres)</label>
            <input type="password" name="password" id="password" required minlength="8">
            
            <label for="password_confirm">Confirme a Nova Senha</label>
            <input type="password" name="password_confirm" id="password_confirm" required>
            
            <button type="submit">Salvar Nova Senha</button>
        </form>
    </div>
</body>
</html>