<?php include_once __DIR__ . '/../_header.php'; // Inclui header ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5"> <div class="card shadow-lg border-0 rounded-lg">
                 <div class="card-header bg-primary text-white text-center py-3">
                    <h1 class="h4 mb-0 fw-bold">Criar Nova Conta</h1>
                </div>
                 <div class="card-body p-4"> <?php
                    // Exibe erros de validação vindos do Controller ($errors)
                    if (isset($errors) && !empty($errors)):
                    ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erro ao registar:</strong>
                            <ul class="mb-0 mt-1" style="padding-left: 1.2rem;"> <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= BASE_URL ?>/auth/register">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome (opcional):</label>
                            <input type="text" id="name" name="name" class="form-control form-control-lg"
                                   value="<?= htmlspecialchars($oldData['name'] ?? '') ?>" placeholder="Seu Nome Completo">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail:</label>
                            <input type="email" id="email" name="email" class="form-control form-control-lg <?php if(isset($errors) && (array_search('E-mail inválido.', $errors) !== false || array_search('E-mail já cadastrado.', $errors) !== false)) echo 'is-invalid'; ?>"
                                   required value="<?= htmlspecialchars($oldData['email'] ?? '') ?>" placeholder="seu.email@exemplo.com">
                            <?php if (isset($errors)): // Bloco para erros inline ?>
                                <?php if(array_search('E-mail inválido.', $errors) !== false): ?>
                                    <div class="invalid-feedback">E-mail inválido.</div>
                                <?php elseif(array_search('E-mail já cadastrado.', $errors) !== false): ?>
                                    <div class="invalid-feedback d-block">E-mail já cadastrado.</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha (mín. 8 caracteres):</label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg <?php if(isset($errors) && array_search('A senha deve ter no mínimo 8 caracteres.', $errors) !== false) echo 'is-invalid'; ?>"
                                   required minlength="8" placeholder="********">
                            <?php if (isset($errors) && array_search('A senha deve ter no mínimo 8 caracteres.', $errors) !== false): ?>
                                <div class="invalid-feedback">A senha deve ter no mínimo 8 caracteres.</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirmar Senha:</label>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control form-control-lg <?php if(isset($errors) && array_search('As senhas não coincidem.', $errors) !== false) echo 'is-invalid'; ?>"
                                   required minlength="8" placeholder="********">
                            <?php if (isset($errors) && array_search('As senhas não coincidem.', $errors) !== false): ?>
                                <div class="invalid-feedback">As senhas não coincidem.</div>
                            <?php endif; ?>
                        </div>

                         <div class="d-grid mb-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-person-plus-fill me-2"></i> Registar</button>
                        </div>
                    </form>
                    <p class="text-center mb-0 small text-secondary"> Já tem uma conta? <a href="<?= BASE_URL ?>/auth/login">Faça Login</a>
                    </p>
                </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui footer ?>