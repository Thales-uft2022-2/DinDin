<?php include_once __DIR__ . '/../_header.php'; // Inclui header ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;"> <div class="col-md-5 col-lg-4"> <div class="card shadow-lg border-0 rounded-lg"> <div class="card-header bg-primary text-white text-center py-3">
                    <h2 class="h4 mb-0 fw-bold">Entrar no DinDin</h2>
                </div>
                <div class="card-body p-4"> <?php
                    // Exibe a mensagem de sucesso do registro, se existir (vem da sessão via Controller)
                    if (isset($success_message) && $success_message):
                    ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($success_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Exibe a mensagem de erro de login, se existir (vem do Controller)
                    if (!empty($error)):
                    ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/auth/login">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg" required autofocus placeholder="seu.email@exemplo.com"> </div>

                        <div class="mb-3"> <label for="password" class="form-label">Senha</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="********">
                        </div>

                        <div class="d-grid mb-3 mt-4"> <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-right me-2"></i> Entrar</button>
                        </div>
                    </form>

                    <div class="text-center small"> <a href="<?= BASE_URL ?>/auth/forgot-password" class="d-block mb-2 text-secondary">Esqueci minha senha</a>
                        <p class="mb-0 text-secondary">Não tem conta? <a href="<?= BASE_URL ?>/auth/register">Cadastre-se</a></p>
                    </div>
                </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui footer ?>