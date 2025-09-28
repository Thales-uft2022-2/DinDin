<?php
// Garante sessão ativa sem gerar Notice
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Mensagem de erro (se vier do AuthController/google_callback)
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Login - DinDin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "Segoe UI", sans-serif; }
        body {
            background: linear-gradient(135deg, #0d6efd, #00bcd4);
            min-height: 100vh; display: flex; justify-content: center; align-items: center;
        }
        .login-container {
            background: #fff; padding: 40px; border-radius: 20px; max-width: 420px; width: 100%;
            box-shadow: 0 8px 20px rgba(0,0,0,.2); text-align: center;
        }
        h2 { margin-bottom: 20px; color: #0d6efd; }
        .form-group { margin-bottom: 15px; text-align: left; }
        label { font-weight: 600; display: block; margin-bottom: 6px; }
        input { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
        button {
            background: #0d6efd; color: #fff; border: none; padding: 12px; width: 100%;
            border-radius: 8px; cursor: pointer; font-weight: 700; transition: .3s;
        }
        button:hover { background: #0056b3; }
        .google-btn {
            display: flex; justify-content: center; align-items: center;
            background: #fff; color: #444; border: 1px solid #ccc; margin-top: 10px;
            text-decoration: none; padding: 12px; border-radius: 8px; font-weight: 600;
        }
        .google-btn img { margin-right: 8px; }
        .error { color: #d00; margin-bottom: 15px; text-align: left; }
        .links { margin-top: 15px; font-size: 14px; }
        .links a { color: #0d6efd; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Entrar no DinDin</h2>

        <?php if ($erro): ?>
            <div class="error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <!-- Login tradicional -->
        <form method="post" action="<?= htmlspecialchars(BASE_URL . '/auth/authenticate') ?>">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" required placeholder="Digite seu e-mail" />
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input id="senha" type="password" name="senha" required minlength="8" placeholder="Digite sua senha" />
            </div>

            <button type="submit">Entrar</button>
        </form>

        <!-- Login com Google -->
        <a class="google-btn" href="<?= htmlspecialchars(BASE_URL . '/auth/google_login.php') ?>">
            <img src="https://developers.google.com/identity/images/g-logo.png" width="20" alt="Google" />
            Entrar com Google
        </a>

        <div class="links">
            <a href="<?= htmlspecialchars(BASE_URL . '/auth/register') ?>">Criar conta</a> |
            <a href="<?= htmlspecialchars(BASE_URL . '/auth/forgot') ?>">Esqueci minha senha</a>
        </div>
    </div>
</body>
</html>