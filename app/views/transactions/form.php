<?php
/** @var array  $data */
/** @var string $action */
/** @var string $title */
/** @var array|null $formErrors */
?>
<h1><?= htmlspecialchars($title) ?></h1>

<?php if (!empty($formErrors)): ?>
  <div style="color:#a00;background:#fee;padding:8px;border:1px solid #f99;margin-bottom:10px;">
    <?php foreach ($formErrors as $e): ?>
      <div>• <?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($action) ?>">
  <?php if (!empty($data['id'])): ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">
  <?php endif; ?>

  <label>Tipo:
    <select name="type" required>
      <option value="income"  <?= $data['type']==='income'  ? 'selected' : '' ?>>Receita</option>
      <option value="expense" <?= $data['type']==='expense' ? 'selected' : '' ?>>Despesa</option>
    </select>
  </label><br><br>

  <label>Categoria:
    <input type="text" name="category" value="<?= htmlspecialchars($data['category']) ?>" required>
  </label><br><br>

  <label>Descrição:
    <input type="text" name="description" value="<?= htmlspecialchars($data['description']) ?>">
  </label><br><br>

  <label>Valor (R$):
    <input type="text" name="amount" inputmode="decimal"
           value="<?= htmlspecialchars($data['amount']) ?>" placeholder="Ex.: 123,45" required>
  </label><br><br>

  <label>Data:
    <input type="date" name="date" value="<?= htmlspecialchars($data['transaction_date']) ?>" required>
  </label><br><br>

  <button type="submit">Salvar</button>
  &nbsp; <a href="<?= htmlspecialchars(BASE_URL . '/transactions') ?>">Voltar</a>
</form>
