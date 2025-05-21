<?php
// views/rrhh/index.php
// $employees con id, full_name, department, active
?>
<h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($title) ?></h1>

<table class="min-w-full bg-white shadow rounded">
  <thead class="bg-gray-100">
    <tr>
      <th class="px-4 py-2">Nombre</th>
      <th class="px-4 py-2">Departamento</th>
      <th class="px-4 py-2">Activo</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($employees)): ?>
      <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">No hay empleados.</td></tr>
    <?php else: ?>
      <?php foreach($employees as $e): ?>
        <tr class="border-t">
          <td class="px-4 py-2"><?= htmlspecialchars($e['full_name']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($e['department']) ?></td>
          <td class="px-4 py-2"><?= $e['active'] ? 'Sí' : 'No' ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
