<?php include_once __DIR__ . '/../_header.php'; // Inclui header ?>

<div class="container">
     <div class="row justify-content-center align-items-center" style="min-height: 80vh;"> <div class="col-md-6 col-lg-5"> <div class="card shadow-lg border-0 rounded-lg text-center">
                 <div class="card-body p-5"> <i class="bi bi-envelope-check-fill text-info display-1 mb-4"></i> <h2 class="h4 fw-bold mb-3">Verifique seu E-mail</h2>
                     <p class="text-muted mb-3"> <?php
                        // A mensagem $message é passada pelo Controller (sendResetLink)
                        // Contém a mensagem genérica do AuthService->requestPasswordReset
                        echo htmlspecialchars($message ?? 'Se um e-mail correspondente for encontrado em nosso sistema, enviaremos um link com as instruções para redefinir sua senha.');
                        ?>
                     </p>
                     <p class="text-muted mb-4 small">Por favor, verifique a sua caixa de entrada e também a pasta de spam/lixo eletrônico. O link é válido por 1 hora.</p>
                     <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary btn-lg px-5"><i class="bi bi-arrow-left me-1"></i> Voltar para o Login</a>
                 </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui footer ?>