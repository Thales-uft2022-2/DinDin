<?php 
include_once __DIR__ . '/../_header.php'; 

// Pega a inicial ou o avatar para a pré-visualização
$userInitial = '?';
$userAvatar = $user['avatar'] ?? null;
if (!$userAvatar && isset($user['name']) && !empty($user['name'])) {
    $userInitial = strtoupper(substr($user['name'], 0, 1));
}
?>

<div class="avatar-lightbox" id="avatar-lightbox">
    <span class="avatar-lightbox-close" title="Fechar">&times;</span>
    <img class="avatar-lightbox-content" id="avatar-lightbox-img">
</div>

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

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0"><i class="bi bi-image-fill me-2"></i> Foto de Perfil</h2>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <div class="avatar-upload-container">
                        <div class="avatar-preview" id="avatar-preview-clickable" title="Ver imagem">
                            <?php if ($userAvatar): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($userAvatar) ?>" alt="Avatar Atual" id="avatar-preview-img">
                            <?php else: ?>
                                <span class="avatar-preview-initial"><?= htmlspecialchars($userInitial) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="avatar-edit-btn" id="avatar-edit-btn" title="Editar foto">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        
                        <div class="avatar-context-menu shadow-lg" id="avatar-context-menu">
                            <button type="button" class="avatar-context-item" id="avatar-view-btn">
                                <i class="bi bi-eye-fill"></i> Ver Imagem
                            </button>
                            <button type="button" class="avatar-context-item" id="avatar-change-btn">
                                <i class="bi bi-upload"></i> Trocar Imagem
                            </button>
                            <?php if ($userAvatar): // Só mostra "Apagar" se tiver uma foto ?>
                            <div class="profile-menu-divider"></div>
                            <button type="button" class="avatar-context-item avatar-context-delete" id="avatar-delete-btn">
                                <i class="bi bi-trash3-fill"></i> Apagar Imagem
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>/profile/update-avatar" enctype="multipart/form-data" id="avatar-upload-form" class="d-none">
                        <input type="file"
                               id="avatar-file-input"
                               name="avatar"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               onchange="document.getElementById('avatar-upload-form').submit();"> 
                               </form>

                    <form method="POST" action="<?= BASE_URL ?>/profile/delete-avatar" id="avatar-delete-form" class="d-none"></form>
                </div>
            </div>

            <div class="card shadow-sm border border-primary">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-person-fill-gear me-2"></i> Dados de Cadastro</h1>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php if (empty($user['name']) || $user['name'] === explode('@', $user['email'])[0]): ?>
                    <p class="text-center text-warning small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Parece que você não definiu um nome. Que tal completar seu perfil?
                    </p>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/profile/update">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo:</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control form-control-lg"
                                   value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">E-mail (não pode ser alterado):</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control form-control-lg"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                   disabled readonly>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Salvar Nome
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