<?php 
// Verifica se há alguma mensagem para exibir
$message = $success ?? $error ?? null; 
$message_type = isset($success) ? 'success' : (isset($error) ? 'error' : '');
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

<div class="home">
    <div class="card form-container" style="max-width: 400px; padding: 30px;">
        <div class="logo">
            <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Logo DinDin">
        </div>

        <h1>Crie sua conta no DinDin 📝</h1>
        <p style="text-align: center; margin-bottom: 25px;">
            Informe seu e-mail e uma senha forte para começar a gerenciar suas finanças.
        </p>
        
        <?php if ($message): ?>
            <div class="message" style="background: <?= $message_type === 'success' ? '#d4edda' : '#f8d7da' ?>; border-color: <?= $message_type === 'success' ? '#c3e6cb' : '#f5c6cb' ?>; color: <?= $message_type === 'success' ? '#155724' : '#721c24' ?>; margin-bottom: 20px;">
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/user/store" method="POST">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="seu.email@exemplo.com">
            
            <label for="password">Senha (mín. 8 caracteres)</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="********">
            
            <button type="submit" class="btn primary">Cadastrar</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Já tem conta? <a href="<?= BASE_URL ?>/auth/login">Faça Login</a>
        </p>
    </div>
</div>