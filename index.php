<?php

include_once 'debug.php';

// Iniciar sesión al principio de la aplicación
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuración de base de datos
include_once "config/database.php";

// Obtener ruta desde URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verificación de autenticación
$public_routes = ['auth.login', 'auth.logout', 'auth.register'];
$route = $controller . '.' . $action;

// Verificar si el usuario está autenticado para rutas protegidas
if (!isset($_SESSION['user_id']) && !in_array($route, $public_routes) && $controller !== '') {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Por defecto ir al dashboard para usuarios autenticados sin ruta específica
if (empty($controller) && isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard');
    exit;
}

// Por defecto ir al login para usuarios no autenticados
if (empty($controller) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Cargar controlador si existe
$controller_file = "controllers/{$controller}_controller.php";

if (file_exists($controller_file)) {
    include_once $controller_file;
    
    // Instanciar controlador
    $controller_class = ucfirst($controller) . "Controller";
    
    if (class_exists($controller_class)) {
        $controller_obj = new $controller_class();
        
        if (method_exists($controller_obj, $action)) {
            // Ejecutar acción del controlador
            $controller_obj->$action();
            exit;
        } else {
            // Método no encontrado
            include_once "views/404.php";
            exit;
        }
    } else {
        // Clase de controlador no encontrada
        include_once "views/404.php";
        exit;
    }
} else {
    // Archivo de controlador no encontrado
    include_once "views/404.php";
    exit;
}
?>