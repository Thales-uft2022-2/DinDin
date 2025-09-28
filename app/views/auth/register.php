<?php
// Caminho: app/views/auth/register.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro - DinDin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="form-container">
    <h1>Crie sua conta no DinDin 🔐</h1>
    
    <?php 
    // Exibe a mensagem de erro que veio da URL
    if (!empty($error)): 
        $errors = explode(' | ', urldecode($error));
        foreach ($errors as $e): ?>
            <p style="color:red; font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($e) ?></p>
        <?php endforeach;
    endif;
    ?>

    <form method="POST" action="<?= BASE_URL ?>/auth/store">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required placeholder="seu.email@exemplo.com">

        <label for="password">Senha (mín. 8 caracteres):</label>
        <input type="password" id="password" name="password" required placeholder="••••••••">

        <button type="submit">Registrar</button>
    </form>
    
    <p style="text-align: center; margin-top: 15px;">
        Já tem uma conta? <a href="#">Faça Login</a>
    </p>
</div>

</body>
</html>