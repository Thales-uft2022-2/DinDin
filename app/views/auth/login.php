<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - DinDin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    </head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <div class="home">
        <div class="card form-container" style="max-width: 450px;">
            
            <div class="logo">
                <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Logo DinDin" style="max-width: 180px;">
            </div>

            <h1>Entrar no DinDin</h1>

            <?php if (!empty($error)): ?>
                <p style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/login" style="box-shadow: none; padding: 0;">
                
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required autofocus>

                <label for="password">Senha</label>
                <input type="password" name="password" id="password" required>

                <button type="submit" class="btn primary" style="width: 100%;">Entrar</button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <p>Não tem conta? <a href="<?= BASE_URL ?>/auth/register">Cadastre-se</a></p>
                <p><a href="<?= BASE_URL ?>/auth/forgot-password">Esqueci minha senha</a></p>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
    
    </body>
</html>