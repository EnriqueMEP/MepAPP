<?php
// views/erp/index.php
// $invoices contiene id, customer, amount, created_at
?>
<h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($title) ?></h1>

<table class="min-w-full bg-white shadow rounded">
  <thead class="bg-gray-100">
    <tr>
      <th class="px-4 py-2">ID</th>
      <th class="px-4 py-2">Cliente</th>
      <th class="px-4 py-2">Importe</th>
      <th class="px-4 py-2">Fecha</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($invoices)): ?>
      <tr><td colspan="4" class="px-4 py-2 text-center text-gray-500">No hay facturas aún.</td></tr>
    <?php else: ?>
      <?php foreach($invoices as $inv): ?>
        <tr class="border-t">
          <td class="px-4 py-2"><?= $inv['id'] ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($inv['customer']) ?></td>
          <td class="px-4 py-2"><?= number_format($inv['amount'],2,',','.') ?>€</td>
          <td class="px-4 py-2"><?= date('d/m/Y', strtotime($inv['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
