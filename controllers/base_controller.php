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
        // Extraer datos para la vista
        extract($data);

        // Generar contenido de la vista
        ob_start();
        include_once __DIR__ . "/../views/{$view}.php";
        $content = ob_get_clean();

        // Inicializar notificaciones
        $unread_count  = 0;
        $notifications = [];

        if (isset($_SESSION['user_id']) && file_exists(__DIR__ . '/../models/message.php')) {
            require_once __DIR__ . '/../models/message.php';
            $messageModel = new Message($this->db);

            try {
                // Recupera las conversaciones recientes
                $convs = $messageModel->getRecentConversations((int)$_SESSION['user_id']);

                // Cuenta total de mensajes no leídos (campo unread_count que retorna el método)
                foreach ($convs as $c) {
                    $unread_count += isset($c['unread_count']) ? (int)$c['unread_count'] : 0;
                }

                // Construye array de notificaciones (hasta 5)
                foreach (array_slice($convs, 0, 5) as $c) {
                    $notifications[] = [
                        'type'    => 'chat',
                        'title'   => $c['other_user_name'] ?? $c['full_name'],
                        'content' => strlen($c['last_message']) > 30
                                     ? substr($c['last_message'], 0, 30).'…'
                                     : $c['last_message'],
                        'time'    => $c['last_message_time'],
                        'url'     => 'index.php?controller=chat&action=index&chat_id=' . $c['chat_id']
                    ];
                }
            } catch (PDOException $e) {
                // Si falla, mantenemos 0 y lista vacía
                $unread_count  = 0;
                $notifications = [];
            }
        }

        // Incluir layout pasando $content, $unread_count y $notifications
        include_once __DIR__ . "/../views/{$layout}.php";

    } catch (Exception $e) {
        if (defined('APP_ENV') && APP_ENV === 'development') {
            echo "<h2>Error en renderizado</h2><pre>{$e->getMessage()}</pre><pre>{$e->getTraceAsString()}</pre>";
        } else {
            echo "Ha ocurrido un error. Intenta más tarde.";
        }
    }
}
}