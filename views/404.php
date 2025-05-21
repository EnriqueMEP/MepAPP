<?php
$title = "Página no encontrada";
ob_start();
?>

<div class="flex items-center justify-center h-full my-16">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-800">404</h1>
        <p class="text-2xl text-gray-600 mt-4">Página no encontrada</p>
        <p class="text-gray-500 mt-2 mb-6">Lo sentimos, la página que buscas no existe.</p>
        <a href="index.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Volver al inicio</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>