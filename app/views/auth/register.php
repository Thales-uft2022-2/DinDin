<?php 
// Verifica se há alguma mensagem para exibir
$message = $success ?? $error ?? null; 
$message_type = isset($success) ? 'success' : (isset($error) ? 'error' : '');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Crie sua Conta - DinDin</title>
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

            <h1>Crie sua conta no DinDin</h1>
            <p style="font-size: 0.95rem; max-width: 300px;">
                Informe seu e-mail e uma senha forte para começar.
            </p>
            
            <?php if ($message): ?>
                <div class="message <?= $message_type === 'success' ? 'success' : 'error' ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/user/store" method="POST">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu.email@exemplo.com">
                
                <label for="password">Senha (mín. 8 caracteres)</label>
                <input type="password" id="password" name="password" required minlength="8" placeholder="********">
                
                <button type="submit" class="btn primary" style="width: 100%;">Cadastrar</button>
            </form>
            
            <p style="margin-top: 20px;">
                Já tem conta? <a href="<?= BASE_URL ?>/auth/login">Faça Login</a>
            </p>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
</body>
</html>