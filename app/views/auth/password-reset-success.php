<?php include_once __DIR__ . '/../_header.php'; // Inclui header ?>

<div class="container">
     <div class="row justify-content-center align-items-center" style="min-height: 80vh;"> <div class="col-md-6 col-lg-5"> <div class="card shadow-lg border-0 rounded-lg text-center">
                 <div class="card-body p-5"> <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i> <h2 class="h4 fw-bold mb-3">Senha Redefinida com Sucesso!</h2>
                     <p class="text-muted mb-4">Sua senha foi alterada. Agora você já pode fazer login utilizando a sua nova senha.</p>
                     <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary btn-lg px-5"><i class="bi bi-box-arrow-in-right me-2"></i> Ir para a Página de Login</a> </div> </div> </div> </div> </div> <?php include_once __DIR__ . '/../_footer.php'; // Inclui footer ?>