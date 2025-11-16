<?php
// Garante que a sessão está ativa para a verificação
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Pega a inicial do usuário ou a foto do avatar
$userInitial = '?';
$userAvatar = $_SESSION['user']['avatar'] ?? null;
if (!$userAvatar && isset($_SESSION['user']['name']) && !empty($_SESSION['user']['name'])) {
    $userInitial = strtoupper(substr($_SESSION['user']['name'], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DinDin - Gestor Financeiro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <!-- ==== NOVO (Chart.js) ==== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const osPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || osPreference;
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
</head>
<body> 
    
    <div class="top-left-controls">
        
        <?php 
        // 1. Logo (Home) - SÓ APARECE LOGADO
        if (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id'])): 
        ?>
            <a href="<?= BASE_URL ?>/home" class="logo-header-link shadow-sm">
                <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.png" alt="DinDin Home" class="logo-header-image">
            </a>
        <?php 
        endif; 
        ?>
        
        <button class="theme-toggle shadow-sm" id="theme-toggle-btn" title="Alternar tema">
            <i class="bi bi-sun-fill"></i>
            <i class="bi bi-moon-stars-fill"></i>
        </button>
    </div>
    
    <?php 
    // Canto Direito: NOVO MENU DE PERFIL (SÓ APARECE SE ESTIVER LOGADO)
    if (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id'])): 
    ?>
    <div class="profile-dropdown-container">
        <button class="profile-btn shadow-sm" id="profile-menu-btn" title="Menu de Perfil">
            <?php if ($userAvatar): ?>
                <img src="<?= BASE_URL . '/' . htmlspecialchars($userAvatar) ?>" alt="Avatar" class="profile-btn-avatar">
            <?php else: ?>
                <span><?= htmlspecialchars($userInitial) ?></span>
            <?php endif; ?>
        </button>
        
        <div class="profile-dropdown-menu shadow-lg" id="profile-menu">
            <div class="profile-menu-header">
                <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                <small class="text-muted"><?= htmlspecialchars($_SESSION['user']['email']) ?></small>
            </div>
            <a href="<?= BASE_URL ?>/profile" class="profile-menu-item">
                <i class="bi bi-person-fill-gear"></i>
                <span>Meu Perfil</span>
            </a>
            <a href="<?= BASE_URL ?>/profile/password" class="profile-menu-item">
                <i class="bi bi-key-fill"></i>
                <span>Redefinir Senha</span>
            </a>
            <div class="profile-menu-divider"></div>
            <a href="<?= BASE_URL ?>/auth/logout" class="profile-menu-item profile-menu-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>
    <?php 
    endif; 
    ?>

    <main class="container py-4">