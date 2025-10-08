<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - DinDin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #00bcd4);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #0d6efd;
        }
        .btn-custom {
            width: 100%;
            background-color: #0d6efd;
            color: #fff;
        }
        .btn-custom:hover {
            background-color: #0b5ed7;
        }
        .extra-links {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Entrar no DinDin</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/auth/login">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-custom">Entrar</button>
        </form>

        <div class="extra-links">
            <p>Não tem conta? <a href="<?= BASE_URL ?>/auth/register">Cadastre-se</a></p>
            <p><a href="<?= BASE_URL ?>/auth/forgot-password">Esqueci minha senha</a></p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>