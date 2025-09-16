<?php /** @var array $transactions */ /** @var string $base */ ?>
<h1>Transações</h1>

<p>
  <a href="<?= htmlspecialchars($base . '/transactions/create') ?>">+ Nova transação</a>
</p>

<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>ID</th>
      <th>Tipo</th>
      <th>Categoria</th>
      <th>Descrição</th>
      <th>Valor (R$)</th>
      <th>Data</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$transactions): ?>
      <tr><td colspan="7" style="text-align:center;">Nenhum registro</td></tr>
    <?php else: foreach ($transactions as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['id']) ?></td>
        <td><?= htmlspecialchars($t['type']) ?></td>
        <td><?= htmlspecialchars($t['category']) ?></td>
        <td><?= htmlspecialchars($t['description'] ?? '') ?></td>
        <td><?= htmlspecialchars(number_format((float)$t['amount'], 2, ',', '.')) ?></td>
        <td><?= htmlspecialchars($t['transaction_date']) ?></td>
        <td>
          <a href="<?= htmlspecialchars($base . '/transactions/edit?id=' . $t['id']) ?>">Editar</a>
        </td>
      </tr>
    <?php endforeach; endif; ?>
  </tbody>
</table>
