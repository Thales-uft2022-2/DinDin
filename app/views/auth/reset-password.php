<?php include_once __DIR__ . '/../_header.php'; // Inclui header ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;"> <div class="col-md-6 col-lg-5"> <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h1 class="h4 mb-0 fw-bold">Crie uma Nova Senha</h1>
                </div>
                 <div class="card-body p-4"> <?php
                     // Exibe erros de validação vindos do Controller (updatePassword)
                     if (isset($errors) && !empty($errors)):
                     ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erro ao redefinir:</strong>
                            <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/auth/update-password">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') // O token vem do controller ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Nova Senha (mín. 8 caracteres)</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg <?php if(isset($errors) && (array_search('A nova senha deve ter no mínimo 8 caracteres.', $errors) !== false || array_search('A nova senha não pode estar em branco.', $errors) !== false)) echo 'is-invalid'; ?>" required minlength="8" placeholder="********">
                             <?php if (isset($errors)): // Bloco para erros inline ?>
                                <?php if(array_search('A nova senha deve ter no mínimo 8 caracteres.', $errors) !== false): ?>
                                    <div class="invalid-feedback">A nova senha deve ter no mínimo 8 caracteres.</div>
                                <?php elseif(array_search('A nova senha não pode estar em branco.', $errors) !== false): ?>
                                     <div class="invalid-feedback">A nova senha não pode estar em branco.</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirme a Nova Senha</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control form-control-lg <?php if(isset($errors) && array_search('As senhas não coincidem.', $errors) !== false) echo 'is-invalid'; ?>" required placeholder="********">
                             <?php if (isset($errors) && array_search('As senhas não coincidem.', $errors) !== false): ?>
                                <div class="invalid-feedback">As senhas não coincidem.</div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid mb-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-key-fill me-2"></i> Salvar Nova Senha</button>
                        </div>
                    </form>
                    <p class="text-center mb-0 small"><a href="<?= BASE_URL ?>/auth/login" class="text-secondary"><i class="bi bi-arrow-left me-1"></i> Voltar para o Login</a></p>
                </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui footer ?>