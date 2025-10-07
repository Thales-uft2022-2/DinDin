<?php 
// Verifica se há alguma mensagem para exibir
$message = $success ?? $error ?? null; 
$message_type = isset($success) ? 'success' : (isset($error) ? 'error' : '');
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

<div class="home" style="
    min-height: 100vh; 
    height: auto;
    display: flex; 
    justify-content: center; 
    align-items: center; 
    padding: 20px;
">
    <div class="card form-container" style="
        max-width: 400px; 
        width: 100%; 
        padding: 30px 25px; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
    ">
        
        <!-- Logo -->
        <div class="logo" style="margin-bottom: 25px;">
            <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Logo DinDin" style="max-width: 220px; width: 100%; height: auto; display: block; margin: 0 auto;">
        </div>

        <!-- Título -->
        <h1 style="text-align: center; color: #007BFF; margin-bottom: 10px; font-size: 1.8rem;">Crie sua conta no DinDin</h1>
        <p style="text-align: center; color: #555; margin-bottom: 20px; font-size: 0.95rem;">
            Informe seu e-mail e uma senha forte para começar a gerenciar suas finanças.
        </p>
        
        <!-- Mensagem -->
        <?php if ($message): ?>
            <div class="message" style="
                background: <?= $message_type === 'success' ? '#d4edda' : '#f8d7da' ?>;
                border-color: <?= $message_type === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;
                color: <?= $message_type === 'success' ? '#155724' : '#721c24' ?>;
                margin-bottom: 15px;
                font-size: 0.9rem;
                text-align: center;
                border-radius: 8px;
                padding: 10px 12px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            ">
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form action="<?= BASE_URL ?>/user/store" method="POST" style="width: 100%; display: flex; flex-direction: column; gap: 12px;">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="seu.email@exemplo.com" style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px;">
            
            <label for="password">Senha (mín. 8 caracteres)</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="********" style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px;">
            
            <button type="submit" class="btn primary" style="width: 100%; padding: 12px; font-size: 15px; border-radius: 8px;">Cadastrar</button>
        </form>
        
        <!-- Link para login -->
        <p style="text-align: center; margin-top: 18px; font-size: 0.9rem; color: #555;">
            Já tem conta? <a href="<?= BASE_URL ?>/auth/login" style="color: #007BFF; font-weight: bold;">Faça Login</a>
        </p>
    </div>
</div>
