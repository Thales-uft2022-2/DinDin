<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

<a href="<?= BASE_URL ?>/home" class="home-logo-link">
    <img src="<?= BASE_URL ?>/images/DinDin_Logo_Option2.jpg" alt="Voltar para a Home">
</a>

<div style="position: fixed; top: 20px; right: 20px; z-index: 1001; display: flex; align-items: center; gap: 15px;">
    
    <?php if (isset($_SESSION['user']['name'])): ?>
        <a href="<?= BASE_URL ?>/user/profile" style="color: #007bff; text-decoration: none; font-weight: bold; background-color: rgba(255,255,255,0.8); padding: 8px 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            👤 <?= htmlspecialchars($_SESSION['user']['name']) ?>
        </a>
    <?php endif; ?>

    <button id="theme-switcher" style="font-size: 1.5rem; background: none; border: none; cursor: pointer;">🌙</button>
</div>

<style>
    body.dark-mode a[href$="/user/profile"] {
        color: #f5f5f5;
        background-color: rgba(44, 44, 44, 0.8);
    }
</style>
