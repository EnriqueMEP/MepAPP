<?php
// controllers/base_controller.php

// Cargar configuración general de la app
require_once __DIR__ . '/../config/app.php';

class BaseController {
    protected $db;
    protected $user;

    public function __construct() {
        // Iniciar sesión sólo si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Conectar a la base de datos
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();

        // Cargar modelo de usuario si hay sesión
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../models/user.php';
            $this->user = new User($this->db);
            $this->user->id = $_SESSION['user_id'];
            $this->user->read_single();
        }
    }

    /**
     * Verificar permisos de usuario para un módulo/acción
     */
    protected function requirePermission($module, $permission_type = 'read') {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?controller=auth&action=login');
            exit;
        }

        if (!$this->user->hasPermission($module, $permission_type)) {
            include_once __DIR__ . '/../views/403.php';
            exit;
        }
    }

    /**
     * Renderizar una vista dentro de un layout
     * @param string $view   Ruta relativa a views, sin .php (e.g. 'dashboard/index')
     * @param array  $data   Variables a extraer para la vista
     * @param string $layout Nombre de layout (archivo en views/, sin extensión)
     */
    protected function render($view, $data = [], $layout = 'layout') {
        try {
            // Extraer datos como variables para la vista
            extract($data);

            // Generar contenido de la vista
            ob_start();
            include_once __DIR__ . "/../views/{$view}.php";
            $content = ob_get_clean();

            // Notificaciones y contador de mensajes no leídos
            $unread_count = 0;
            $notifications = [];

            if (isset($_SESSION['user_id']) && file_exists(__DIR__ . '/../models/message.php')) {
                require_once __DIR__ . '/../models/message.php';
                $messageModel = new Message($this->db);

                try {
                    $unread_count = $messageModel->getUnreadCount($_SESSION['user_id']);
                    $notifications = $messageModel->getRecentNotifications($_SESSION['user_id'], 5);
                } catch (PDOException $e) {
                    // Si la columna/tabla no existe o hay error en modelo
                    $unread_count = 0;
                    $notifications = [];
                }
            }

            // Incluir layout final
            include_once __DIR__ . "/../views/{$layout}.php";

        } catch (Exception $e) {
            // Mostrar mensaje de error en desarrollo
            if (defined('APP_ENV') && APP_ENV === 'development') {
                echo "<h2>Error en renderizado</h2>";
                echo "<pre>" . $e->getMessage() . "</pre>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            } else {
                echo "Ha ocurrido un error. Intenta más tarde.";
            }
        }
    }
}
