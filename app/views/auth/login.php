<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - DinDin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <style>
        /* Estilos específicos da página de login podem ficar aqui ou no style.css */
        body {
            /* Mantive seu gradiente, mas removi height: 100vh para permitir rolagem se necessário */
            background: linear-gradient(135deg, #0d6efd, #00bcd4);
            /* height: 100vh; */ /* Removido */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh; /* Garante que o gradiente cubra a tela */
            padding: 20px; /* Adiciona algum espaço */
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
            padding: 10px; /* Aumenta um pouco o botão */
            font-size: 1rem; /* Tamanho da fonte */
        }
        .btn-custom:hover {
            background-color: #0b5ed7;
            color: #fff; /* Garante que a cor do texto não mude */
        }
        .extra-links {
            text-align: center;
            margin-top: 15px;
        }
        .extra-links a {
            color: #0d6efd;
            text-decoration: none;
        }
        .extra-links a:hover {
            text-decoration: underline;
        }
        /* Estilo para a mensagem de sucesso */
        .alert-success-custom {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Entrar no DinDin</h2>

        <?php
        // // Verifica se a variável $success_message foi passada pelo controller
        // (ela contém a mensagem que estava na sessão após o registro)
        if (isset($success_message) && $success_message):
        ?>
            <div class="alert alert-success-custom" role="alert">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        
        <?php
        // Bloco de erro que você já tinha
        if (!empty($error)):
        ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
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
            <p><a href="<?= BASE_URL ?>/auth/forgot-password">Esqueci minha senha</a></p>
            <p>Não tem conta? <a href="<?= BASE_URL ?>/auth/register">Cadastre-se</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>