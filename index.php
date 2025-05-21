<?php
session_start();
include_once 'config/database.php';

// Determina controlador y acción
$controller = $_GET['controller'] ?? 'chat';
$action     = $_GET['action']     ?? 'index';

// Ruta al controlador
$controllerFile = "controllers/{$controller}_controller.php";
if (! file_exists($controllerFile)) {
    // podrías redirigir o mostrar 404
    die('Controlador no encontrado');
}
require_once $controllerFile;
// Instancia y llama a la acción
$class = ucfirst($controller) . 'Controller';
$ctrl  = new $class();
if (! method_exists($ctrl, $action)) {
    die('Acción no encontrada');
}
$ctrl->$action();
