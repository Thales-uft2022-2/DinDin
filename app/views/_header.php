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

    <!-- 
      SCRIPT ANTI-FLICKER (Impede o "flash" de tema claro)
      Deve ser colocado no <head> antes de todo o resto.
    -->
    <script>
        (function() {
            // Verifica a preferência salva no localStorage
            const savedTheme = localStorage.getItem('theme');
            // Verifica a preferência do sistema operacional
            const osPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            // Aplica o tema salvo ou a preferência do SO
            const theme = savedTheme || osPreference;
            // Define o atributo [data-bs-theme] no <html>
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
</head>
<!-- 
  A classe 'bg-light' foi REMOVIDA daqui.
  O 'style.css' agora controla a cor de fundo com variáveis.
-->
<body> 
    
    <!-- NOVO BOTÃO DE TEMA -->
    <button class="theme-toggle shadow-sm" id="theme-toggle-btn" title="Alternar tema">
        <i class="bi bi-sun-fill"></i> <!-- Ícone do tema claro (Sol) -->
        <i class="bi bi-moon-stars-fill"></i> <!-- Ícone do tema escuro (Lua) -->
    </button>
    
    <!-- Seu logo existente -->
    <a href="<?= BASE_URL ?>/home" class="logo-header-link shadow-sm">
        <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.png" alt="DinDin Home" class="logo-header-image">
    </a>

    <main class="container py-4">