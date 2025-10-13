<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8"> 
    <title>Recuperar Senha - DinDin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css"> 
</head>
<body>
    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <div class="form-container">
        <h1>Recuperar Senha</h1>
        <p>Informe seu e-mail cadastrado e enviaremos um link para você redefinir sua senha.</p>
        <form method="POST" action="<?= BASE_URL ?>/auth/send-reset-link">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" required autofocus>
            <button type="submit">Enviar Link de Redefinição</button>
        </form>
        <p style="text-align: center; margin-top: 15px;"><a href="<?= BASE_URL ?>/auth/login">Voltar para o Login</a></p>
    </div>
</body>
</html>