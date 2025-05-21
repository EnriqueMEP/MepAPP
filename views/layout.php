<?php
// views/layout.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>

  <!-- Tailwind y tus estilos compilados -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= CSS_URL ?>styles.css?v=<?= APP_VERSION ?>">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-green-800 text-white shadow-md">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <a href="<?= BASE_URL ?>index.php?controller=dashboard&action=index" class="text-xl font-bold"><?= APP_NAME ?></a>
      <nav class="space-x-6">
        <a href="<?= BASE_URL ?>index.php?controller=dashboard&action=index" class="<?= ($_GET['controller'] ?? '') === 'dashboard' ? 'underline' : '' ?>">Dashboard</a>
        <a href="<?= BASE_URL ?>index.php?controller=projects&action=index" class="<?= ($_GET['controller'] ?? '') === 'projects' ? 'underline' : '' ?>">Proyectos</a>
        <a href="<?= BASE_URL ?>index.php?controller=crm&action=index" class="<?= ($_GET['controller'] ?? '') === 'crm' ? 'underline' : '' ?>">CRM</a>
        <a href="<?= BASE_URL ?>index.php?controller=erp&action=index" class="<?= ($_GET['controller'] ?? '') === 'erp' ? 'underline' : '' ?>">ERP</a>
        <a href="<?= BASE_URL ?>index.php?controller=rrhh&action=index" class="<?= ($_GET['controller'] ?? '') === 'rrhh' ? 'underline' : '' ?>">RRHH</a>
        <a href="<?= BASE_URL ?>index.php?controller=chat&action=index" class="<?= ($_GET['controller'] ?? '') === 'chat' ? 'underline' : '' ?>">Chat</a>
      </nav>
      <div class="flex items-center space-x-4">
        <!-- Notificaciones -->
        <div class="relative">
          <a href="<?= BASE_URL ?>index.php?controller=notifications&action=index">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <?php if (!empty($unread_count)): ?>
              <span class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full w-4 h-4 flex items-center justify-center">
                <?= $unread_count ?>
              </span>
            <?php endif; ?>
          </a>
        </div>
        <!-- Usuario -->
        <div class="relative group">
          <button class="flex items-center space-x-2 bg-green-700 px-3 py-1 rounded-md">
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center font-semibold">
              <?= strtoupper(substr($_SESSION['user_name'],0,1)) ?>
            </div>
            <span class="hidden md:inline"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <i data-lucide="chevron-down" class="w-4 h-4"></i>
          </button>
          <div class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="<?= BASE_URL ?>index.php?controller=auth&action=logout" class="block px-4 py-2 hover:bg-gray-100">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="flex-1 container mx-auto px-4 py-6">
    <?= $content ?>
  </main>

  <!-- Footer -->
  <footer class="bg-white py-4 border-t border-gray-200">
    <div class="container mx-auto px-4 flex justify-between text-sm text-gray-500">
      <div>© <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</div>
      <div>v<?= APP_VERSION ?> | <a href="#" class="text-green-600 hover:underline">Soporte</a> | <a href="#" class="text-green-600 hover:underline">Documentación</a></div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>lucide.createIcons()</script>
  <script src="<?= JS_URL ?>main.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>
