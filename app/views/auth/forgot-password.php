<?php include_once __DIR__ . '/../_header.php'; // Inclui header ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;"> <div class="col-md-6 col-lg-5"> <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h1 class="h4 mb-0 fw-bold">Recuperar Senha</h1>
                </div>
                <div class="card-body p-4"> <p class="text-center text-muted mb-4 small">Informe o seu e-mail cadastrado abaixo. Se ele estiver em nosso sistema, enviaremos um link para você criar uma nova senha.</p>
                    <form method="POST" action="<?= BASE_URL ?>/auth/send-reset-link">
                        <div class="mb-4"> <label for="email" class="form-label visually-hidden">E-mail</label> <input type="email" name="email" id="email" class="form-control form-control-lg" required autofocus placeholder="Digite seu e-mail">
                        </div>
                         <div class="d-grid mb-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-envelope-fill me-2"></i> Enviar Link de Redefinição</button>
                        </div>
                    </form>
                    <p class="text-center mb-0 small"><a href="<?= BASE_URL ?>/auth/login" class="text-secondary"><i class="bi bi-arrow-left me-1"></i> Voltar para o Login</a></p>
                </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui footer ?>