<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar - DinDin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    </head>
<body>
    <div class="form-container">
        <h1>Criar Nova Conta</h1>

        <?php
        // Verifica se $errors foi definida pelo controller antes de usar
        if (isset($errors) && !empty($errors)):
        ?>
            <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <strong>Erro ao registrar:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/auth/register">
            <label for="name">Nome (opcional):</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($oldData['name'] ?? '') ?>">

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($oldData['email'] ?? '') ?>">

            <label for="password">Senha (mín. 8 caracteres):</label>
            <input type="password" id="password" name="password" required minlength="8">

            <label for="password_confirm">Confirmar Senha:</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8">

            <button type="submit">Registrar</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Já tem uma conta? <a href="<?= BASE_URL ?>/auth/login">Faça Login</a>
        </p>
    </div>
</body>
</html>