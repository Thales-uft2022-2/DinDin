<?php 
include_once __DIR__ . '/../_header.php'; 
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <?php
            // Exibe a mensagem de SUCESSO ou ERRO
            if (isset($flashMessage) && $flashMessage):
                $alertType = ($flashMessage['type'] === 'success') ? 'success' : 'danger';
            ?>
                <div class="alert alert-<?= $alertType ?> alert-dismissible fade show mb-4" role="alert">
                    <?= htmlspecialchars($flashMessage['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm" id="change-password">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-key-fill me-2"></i> Alterar Senha</h1>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="<?= BASE_URL ?>/profile/change-password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Senha Atual:</label>
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control form-control-lg <?php if(isset($validationErrors['current_password'])) echo 'is-invalid'; ?>"
                                   required>
                            <?php if(isset($validationErrors['current_password'])): ?>
                                <div class="invalid-feedback d-block"><?= $validationErrors['current_password'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova Senha (mín. 8 caracteres):</label>
                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   class="form-control form-control-lg <?php if(isset($validationErrors['new_password'])) echo 'is-invalid'; ?>"
                                   required>
                            <?php if(isset($validationErrors['new_password'])): ?>
                                <div class="invalid-feedback d-block"><?= $validationErrors['new_password'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmar Nova Senha:</label>
                            <input type="password"
                                   id="confirm_password"
                                   name="confirm_password"
                                   class="form-control form-control-lg <?php if(isset($validationErrors['confirm_password'])) echo 'is-invalid'; ?>"
                                   required>
                            <?php if(isset($validationErrors['confirm_password'])): ?>
                                <div class="invalid-feedback d-block"><?= $validationErrors['confirm_password'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= BASE_URL ?>/home" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Voltar ao Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Salvar Nova Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
include_once __DIR__ . '/../_footer.php'; 
?>