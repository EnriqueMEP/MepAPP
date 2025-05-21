<?php
// Archivo: index.php (Front Controller)

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cargar configuración de la app
require_once __DIR__ . '/config/app.php';

// Iniciar sesión
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Redirigir usuarios autenticados que intenten acceder al login
if (
    isset($_SESSION['user_id']) &&
    isset($_GET['controller']) &&
    strtolower($_GET['controller']) === 'auth' &&
    in_array(strtolower($_GET['action'] ?? ''), ['login', 'register'], true)
) {
    header('Location: index.php?controller=dashboard&action=index');
    exit;
}

// Configuración de base de datos
require_once __DIR__ . '/config/database.php';

// Leer controlador y acción desde la URL
$controller = !empty($_GET['controller']) ? strtolower($_GET['controller']) : '';
$action     = !empty($_GET['action']) ? strtolower($_GET['action']) : 'index';
$route      = "$controller.$action";

// Rutas públicas (sin sesión)
$publicRoutes = [
    'auth.login',
    'auth.logout',
    'auth.register'
];

// Si ruta protegida y no hay sesión, forzar login
if (!isset($_SESSION['user_id']) && !in_array($route, $publicRoutes, true)) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Si no se especificó controlador, redirigir según estado de sesión
if (empty($controller)) {
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php?controller=dashboard&action=index');
    } else {
        header('Location: index.php?controller=auth&action=login');
    }
    exit;
}

// Ruta al archivo del controlador
$controllerFile = __DIR__ . "/controllers/{$controller}_controller.php";

if (!file_exists($controllerFile)) {
    // 404 controlador no existe
    include_once __DIR__ . '/views/404.php';
    exit;
}

require_once $controllerFile;
$className = ucfirst($controller) . 'Controller';

if (!class_exists($className)) {
    include_once __DIR__ . '/views/404.php';
    exit;
}

$controllerObj = new $className();

if (!method_exists($controllerObj, $action)) {
    include_once __DIR__ . '/views/404.php';
    exit;
}

// Ejecutar acción
$controllerObj->{$action}();
exit;
