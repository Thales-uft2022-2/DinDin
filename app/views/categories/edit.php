<?php include_once __DIR__ . '/../_header.php';// Inclui o topo [cite: 660]

// Exibe mensagens flash (sucesso ou erro) vindas da Sessão (Controller)
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];

    unset($_SESSION['flash_message']); // Limpa a mensagem após exibir [cite: 661]
    $alertType = ($flashMessage['type'] === 'success') ? 'success' : 'danger';

    
    // Adiciona margem inferior (mb-4) ao alerta
    echo '<div class="alert alert-' . $alertType . ' alert-dismissible fade show mb-4" role="alert">';

    echo htmlspecialchars($flashMessage['message']);

    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

    echo '</div>';
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="bi bi-pencil-fill me-2"></i> Editar Categoria</h1>
                </div>
                <div class="card-body p-4">
                    <?php
                    // Exibe erros de validação (mesma lógica do create)
                    if (isset($errors) && !empty($errors)):

                    ?>
                        <div class="alert alert-danger mb-3" role="alert">

                            <strong>Por favor, corrija os seguintes erros:</strong>
                            <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">

                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>

                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= BASE_URL ?>/categories/update">
                        
                        <input type="hidden" name="id" value="<?= htmlspecialchars($oldData['id'] ?? 0) ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da Categoria:</label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control form-control-lg <?php if(isset($errors) && (array_search('O nome da categoria é obrigatório.', $errors) !== false || array_search('Já existe uma categoria com este nome e tipo.', $errors) !== false)) echo 'is-invalid'; ?>"

                                   required
                                   maxlength="100"

                                   value="<?= htmlspecialchars($oldData['name'] ?? '') ?>"

                                   placeholder="Ex: Supermercado, Salário, Lazer">
                                   
                            <?php if (isset($errors)): // Bloco para erros inline[cite: 673]?>
                                <?php if(array_search('O nome da categoria é obrigatório.', $errors) !== false): ?>
                                    <div class="invalid-feedback">O nome da categoria é obrigatório.</div>

                                <?php elseif(array_search('Já existe uma categoria com este nome e tipo.', $errors) !== false): ?>
                                    <div class="invalid-feedback d-block">Já existe uma categoria com este nome e tipo.</div>

                                <?php endif; ?>
                             <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="form-label">Tipo da Categoria:</label>

                            <select id="type" name="type" class="form-select form-select-lg <?php if(isset($errors) && array_search('O tipo da categoria deve ser "income" (Receita) ou "expense" (Despesa).', $errors) !== false) echo 'is-invalid'; ?>" required>

                                
                                <option value="" disabled <?= !isset($oldData['type']) || $oldData['type']=='' ? 'selected' : '' ?>>-- Selecione o Tipo --</option>

                                
                                <option value="income" <?= (isset($oldData['type']) && $oldData['type'] === 'income') ? 'selected' : '' ?>>Receita (Income)</option>

                                <option value="expense" <?= (isset($oldData['type']) && $oldData['type'] === 'expense') ? 'selected' : '' ?>>Despesa (Expense)</option>

                            </select>
                            
                            <?php if (isset($errors) && array_search('O tipo da categoria deve ser "income" (Receita) ou "expense" (Despesa).', $errors) !== false): ?>
                                <div class="invalid-feedback">Por favor, selecione um tipo válido.</div>

                             <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                             <a href="<?= BASE_URL ?>/categories" class="btn btn-outline-secondary">
                                 <i class="bi bi-arrow-left me-1"></i> Cancelar / Voltar
                             </a>
                             
                             <button type="submit" class="btn btn-primary">

                               <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../_footer.php';// Inclui o rodapé[cite: 686]?>
