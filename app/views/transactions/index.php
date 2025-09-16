<?php
/** Variáveis vindas do controller:
 * @var array  $transactions
 * @var string $base
 * @var int    $total
 * @var int    $perPage
 * @var int    $currentPage
 * @var array  $persist  ['q','type','date_from','date_to','amount_min','amount_max']
 */

// helper para montar links mantendo a query atual
function build_link(string $path, array $extra = []): string {
    $params = array_merge($_GET, $extra);
    $qs = http_build_query($params);
    return htmlspecialchars($path . ($qs ? ('?' . $qs) : ''));
}

$totalPages = max(1, (int)ceil(($total ?? 0) / ($perPage ?? 10)));
$currentPage = max(1, (int)($currentPage ?? 1));
?>
<h1>Transações</h1>

<!-- Botão Nova Transação -->
<div style="margin-bottom:12px;">
  <a href="<?= htmlspecialchars($base . '/transactions/create') ?>"
     style="padding:8px 14px;background:#1976d2;color:#fff;text-decoration:none;border-radius:4px;">
    ➕ Nova Transação
  </a>
</div>

<!-- info rápida -->
<div style="margin:8px 0;">
  <em>Total filtrado: <?= (int)$total ?> • Página <?= (int)$currentPage ?> de <?= (int)$totalPages ?></em>
</div>

<!-- Botões rápidos de filtro -->
<div style="margin-bottom:12px;">
  <a href="<?= build_link($base . '/transactions', ['type'=>'Receita',  'page'=>1]) ?>"
     style="padding:6px 12px;background:#2d8f2d;color:#fff;text-decoration:none;border-radius:4px;">
    💰 Receitas
  </a>
  <a href="<?= build_link($base . '/transactions', ['type'=>'Despesa', 'page'=>1]) ?>"
     style="padding:6px 12px;background:#c62828;color:#fff;text-decoration:none;border-radius:4px;margin-left:6px;">
    🛒 Despesas
  </a>
  <a href="<?= htmlspecialchars($base . '/transactions') ?>"
     style="padding:6px 12px;background:#555;color:#fff;text-decoration:none;border-radius:4px;margin-left:6px;">
    🔄 Todos
  </a>
</div>

<!-- Filtro / Busca avançada -->
<form method="get" action="<?= htmlspecialchars($base . '/transactions') ?>"
      style="padding:10px;border:1px solid #ddd;margin-bottom:12px;">
  <strong>Filtro / Busca avançada</strong><br><br>

  <label>Texto (categoria/descrição):
    <input type="text" name="q" value="<?= htmlspecialchars($persist['q'] ?? '') ?>" placeholder="ex.: aluguel, consultoria">
  </label>
  &nbsp;&nbsp;

  <label>Tipo:
    <select name="type">
      <option value="">(todos)</option>
      <option value="Receita"  <?= (($persist['type'] ?? '')==='Receita')  ? 'selected' : '' ?>>Receita</option>
      <option value="Despesa" <?= (($persist['type'] ?? '')==='Despesa') ? 'selected' : '' ?>>Despesa</option>
    </select>
  </label>
  &nbsp;&nbsp;

  <label>Data de:
    <input type="date" name="date_from" value="<?= htmlspecialchars($persist['date_from'] ?? '') ?>">
  </label>
  <label>até:
    <input type="date" name="date_to" value="<?= htmlspecialchars($persist['date_to'] ?? '') ?>">
  </label>
  &nbsp;&nbsp;

  <label>Valor mín.:
    <input type="text" name="amount_min" inputmode="decimal" value="<?= htmlspecialchars($persist['amount_min'] ?? '') ?>" style="width:100px;">
  </label>
  <label>máx.:
    <input type="text" name="amount_max" inputmode="decimal" value="<?= htmlspecialchars($persist['amount_max'] ?? '') ?>" style="width:100px;">
  </label>
  &nbsp;&nbsp;

  <button type="submit">Aplicar</button>
  &nbsp;
  <a href="<?= htmlspecialchars($base . '/transactions') ?>">Limpar</a>
</form>

<!-- Tabela -->
<table border="1" cellpadding="6" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th>ID</th>
      <th>Tipo</th>
      <th>Categoria</th>
      <th>Descrição</th>
      <th style="text-align:right;">Valor (R$)</th>
      <th>Data</th>
      <th style="width:80px;">Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($transactions)): ?>
      <tr><td colspan="7" style="text-align:center;">Nenhum registro encontrado</td></tr>
    <?php else: foreach ($transactions as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['id']) ?></td>
        <td><?= htmlspecialchars($t['type']) ?></td>
        <td><?= htmlspecialchars($t['category']) ?></td>
        <td><?= htmlspecialchars($t['description'] ?? '') ?></td>
        <td style="text-align:right;"><?= htmlspecialchars(number_format((float)$t['amount'], 2, ',', '.')) ?></td>
        <td><?= htmlspecialchars($t['transaction_date']) ?></td>
        <td>
          <a href="<?= htmlspecialchars($base . '/transactions/edit?id=' . urlencode($t['id'])) ?>">Editar</a>
        </td>
      </tr>
    <?php endforeach; endif; ?>
  </tbody>
</table>

<!-- Paginação -->
<?php if ($totalPages > 1): ?>
  <div style="margin-top:10px;">
    <?php for ($p=1; $p<=$totalPages; $p++): ?>
      <?php if ($p == $currentPage): ?>
        <strong>[<?= $p ?>]</strong>
      <?php else: ?>
        <a href="<?= build_link($base . '/transactions', ['page'=>$p]) ?>"><?= $p ?></a>
      <?php endif; ?>
      &nbsp;
    <?php endfor; ?>
  </div>
<?php endif; ?>
