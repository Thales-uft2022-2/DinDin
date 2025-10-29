<?php include_once __DIR__ . '/../_header.php'; // Inclui o topo

// Exibe mensagens flash (sucesso ou erro) vindas da Sessão (ex: ao criar ou editar)
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Limpa a mensagem após exibir
    $alertType = ($flashMessage['type'] === 'success') ? 'success' : 'danger';
    
    echo '<div class="alert alert-' . $alertType . ' alert-dismissible fade show mb-4" role="alert">';
    echo htmlspecialchars($flashMessage['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-tags-fill me-2 text-primary"></i> Minhas Categorias</h1>
        <a href="<?= BASE_URL ?>/categories/create" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Nova Categoria
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            
            <?php if (empty($categories)): ?>
                <div class="text-center p-4">
                    <p class="lead text-muted">Nenhuma categoria cadastrada ainda.</p>
                    <p>Clique em "Nova Categoria" para começar a organizar suas finanças.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Nome da Categoria</th>
                                <th scope="col">Tipo</th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td data-label="Nome"><?= htmlspecialchars($category['name']) ?></td>
                                    
                                    <td data-label="Tipo">
                                        <span class="badge <?= $category['type'] === 'income' ? 'text-bg-success' : 'text-bg-danger' ?>">
                                            <?= $category['type'] === 'income' ? '<i class="bi bi-arrow-up-circle me-1"></i>Receita' : '<i class="bi bi-arrow-down-circle me-1"></i>Despesa' ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-center table-actions" data-label="Ações">
                                        <a href="<?= BASE_URL ?>/categories/edit?id=<?= $category['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Editar">
                                           <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        
                                        <a href="<?= BASE_URL ?>/categories/delete?id=<?= $category['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir a categoria \'<?= htmlspecialchars($category['name']) ?>\'?')">
                                           <i class="bi bi-trash3-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/home" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar ao Dashboard
        </a>
    </div>
    
</div>

<?php include_once __DIR__ . '/../_footer.php'; // Inclui o rodapé ?>
