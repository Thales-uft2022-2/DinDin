<?php
// app/views/user/switch-accounts.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Troca de Contas - DinDin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

    <script src="<?= BASE_URL ?>/js/theme-switcher.js"></script>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="form-container" style="max-width: 650px; margin-top: 50px;">
        <h1>🔄 Trocar de Conta</h1>
        <p>Selecione o perfil que deseja acessar ou adicione um novo.</p>

        <?php if (isset($success)): ?>
            <div class="message success"><p><?= htmlspecialchars($success) ?></p></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><p><?= htmlspecialchars($error) ?></p></div>
        <?php endif; ?>
        
        <div class="account-list" style="margin-top: 20px;">
            <?php if (empty($associatedAccounts)): ?>
                <div class="message error"><p>Nenhuma conta associada encontrada. Crie mais usuários para testar.</p></div>
            <?php else: ?>
                <?php foreach ($associatedAccounts as $account): ?>
                    <div class="account-card <?= isset($account['current']) ? 'current-account' : '' ?>" 
                        style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 10px; background-color: <?= isset($account['current']) ? '#e6f7ff' : '#f9f9f9' ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        
                        <div>
                            <p style="font-weight: bold; margin: 0; color: <?= isset($account['current']) ? '#007bff' : '#333' ?>;">
                                👤 <?= htmlspecialchars($account['name']) ?> 
                                <?= isset($account['current']) ? '(Perfil Atual)' : '' ?>
                            </p>
                            <p style="font-size: 0.9rem; color: #666; margin: 0;"><?= htmlspecialchars($account['email']) ?></p>
                        </div>
                        
                        <?php if (!isset($account['current'])): ?>
                            <!-- Redireciona para CONFIRMAÇÃO DE SENHA -->
                            <a href="<?= BASE_URL ?>/user/confirm-switch?id=<?= $account['id'] ?>" class="btn primary" style="padding: 8px 15px;">
                                Entrar
                            </a>
                        <?php else: ?>
                            <button disabled class="btn secondary" style="padding: 8px 15px; background-color: #ccc; cursor: default;">
                                Atual
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- CORREÇÃO ESSENCIAL: O botão Adicionar Nova Conta agora chama o LOGOUT -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="<?= BASE_URL ?>/auth/logout" class="btn primary" style="padding: 10px 20px;">
                ➕ Adicionar Nova Conta
            </a>
        </div>
        
        <p style="text-align: center; margin-top: 20px;"><a href="<?= BASE_URL ?>/home">Voltar para a Home</a></p>
    </div>
</body>
</html>