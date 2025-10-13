<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - DinDin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body>

    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="form-container" style="margin-top: 50px;">
        <h1>Meu Perfil</h1>

        <?php if (isset($success)): ?>
            <div class="message success"><p><?= htmlspecialchars($success) ?></p></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><p><?= htmlspecialchars($error) ?></p></div>
        <?php endif; ?>
        
        <form action="<?= BASE_URL ?>/user/update" method="POST">
            
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
            
            <label for="email">E-mail (não pode ser alterado)</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
            
            <p style="font-size: 0.9rem; color: #666; margin-top: 20px;">Deixe os campos de senha em branco se não quiser alterá-la.</p>
            
            <label for="password">Nova Senha (mín. 8 caracteres)</label>
            <input type="password" id="password" name="password" minlength="8">
            
            <label for="password_confirm">Confirmar Nova Senha</label>
            <input type="password" id="password_confirm" name="password_confirm">
            
            <button type="submit" class="btn primary" style="width: 100%;">Salvar Alterações</button>
        </form>
    </div>

    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
</body>
</html>
