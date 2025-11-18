<?php
// Variáveis esperadas do controller:
// $transaction (array)
// $categories (array)
// $action (string)
?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        
        <div class="card shadow-sm border border-primary">
            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Transação #<?= htmlspecialchars($transaction['id']) ?></h1>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form method="post" action="<?= htmlspecialchars($action) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($transaction['id']) ?>">
                    
                    <!-- Linha 1: Tipo e Categoria -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Tipo:*</label>
                            <select name="type" id="type" class="form-select form-select-lg" required>
                                <option value="income" <?= $transaction['type'] === 'income' ? 'selected' : '' ?>>Receita</option>
                                <option value="expense" <?= $transaction['type'] === 'expense' ? 'selected' : '' ?>>Despesa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Categoria:*</label>
                            <select name="category" id="category" class="form-select form-select-lg" required>
                                <option value="">-- Selecione uma categoria --</option>
                                <?php
                                if (!empty($categories)) {
                                    foreach ($categories as $cat) {
                                        // Verifica se esta é a categoria salva
                                        $selected = ($cat['name'] === $transaction['category']) ? 'selected' : '';
                                        
                                        echo '<option value="' . htmlspecialchars($cat['name']) . '" '
                                            . 'data-type="' . htmlspecialchars($cat['type']) . '" '
                                            . $selected . '>'
                                            . htmlspecialchars($cat['name'])
                                            . ' (' . ($cat['type'] === 'income' ? 'Receita' : 'Despesa') . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Linha 2: Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição (Opcional):</label>
                        <input type="text" id="description" name="description" class="form-control form-control-lg"
                               value="<?= htmlspecialchars($transaction['description']) ?>"
                               placeholder="Ex: Aluguel, Salário, Compras do mês">
                    </div>

                    <!-- Linha 3: Valor e Data -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Valor (R$):*</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" id="amount" name="amount" class="form-control"
                                       value="<?= htmlspecialchars($transaction['amount']) ?>"
                                       required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date" class="form-label">Data:*</label>
                            <input type="date" id="date" name="date" class="form-control form-control-lg"
                                   value="<?= htmlspecialchars($transaction['date']) ?>"
                                   required>
                        </div>
                    </div>

                    <!-- Linha 4: Botões -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?= BASE_URL ?>/transactions" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para filtrar as categorias -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const categorySelect = document.getElementById('category');
        
        // Salva todas as opções de categoria originais
        const allOptions = Array.from(categorySelect.options).filter(opt => opt.value !== '');
        // Guarda a categoria que estava selecionada ao carregar a página
        const currentCategoryValue = "<?= htmlspecialchars($transaction['category']) ?>";

        function filterCategories() {
            const selectedType = typeSelect.value;
            
            // Limpa o select (mantendo a primeira opção "Selecione...")
            categorySelect.innerHTML = '<option value="">-- Selecione uma categoria --</option>';

            if (selectedType) {
                // Adiciona apenas as opções do tipo correto
                allOptions.forEach(opt => {
                    if (opt.dataset.type === selectedType) {
                        // Recria a opção para evitar problemas de DOM
                        const newOption = new Option(opt.text, opt.value);
                        newOption.dataset.type = opt.dataset.type;
                        
                        // Se era a que estava selecionada, E o tipo bate, mantém selecionada
                        if (opt.value === currentCategoryValue && opt.dataset.type === selectedType) {
                            newOption.selected = true;
                        }
                        categorySelect.appendChild(newOption);
                    }
                });
                categorySelect.disabled = false;
            } else {
                // Se nenhum tipo for selecionado, desabilita e reseta
                categorySelect.innerHTML = '<option value="">-- Selecione o tipo primeiro --</option>';
                categorySelect.disabled = true;
            }
        }

        // Adiciona o evento
        typeSelect.addEventListener('change', filterCategories);

        // Chama a função uma vez no início para configurar o estado inicial
        // (Isto irá filtrar as categorias para o tipo 'income' ou 'expense' já selecionado)
        filterCategories();
    });
</script>