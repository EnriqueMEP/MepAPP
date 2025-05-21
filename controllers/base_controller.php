<?php
// controllers/base_controller.php
class BaseController {
    protected $db;
    protected $user;
    
    public function __construct() {
        // Iniciar sesión solo si no está ya iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Conectar a la base de datos
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Cargar modelo de usuario si hay sesión
        if (isset($_SESSION['user_id'])) {
            require_once 'models/user.php';
            $this->user = new User($this->db);
            $this->user->id = $_SESSION['user_id'];
            $this->user->read_single();
        }
    }
    
    // Verificar permisos
    protected function requirePermission($module, $permission_type = 'read') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        if (!$this->user->hasPermission($module, $permission_type)) {
            include_once 'views/403.php';
            exit;
        }
    }
    
    // Renderizar vistas con layout
    protected function render($view, $data = [], $layout = 'layout') {
        try {
            // Extraer datos para hacerlos disponibles en la vista
            extract($data);
            
            // Iniciar buffer de salida
            ob_start();
            
            // Incluir la vista
            include_once "views/{$view}.php";
            
            // Obtener contenido del buffer
            $content = ob_get_clean();
            
            // Obtener contador de mensajes no leídos si el usuario está autenticado
            $unread_count = 0;
            $notifications = [];
            
            if (isset($_SESSION['user_id'])) {
                try {
                    // Verificar si existe el modelo de mensajes
                    if (file_exists('models/message.php')) {
                        require_once 'models/message.php';
                        $message_model = new Message($this->db);
                        
                        // Verificar con una consulta simple si la tabla existe
                        try {
                            $check_table = "SHOW TABLES LIKE 'messages'";
                            $table_result = $this->db->query($check_table);
                            
                            if ($table_result->rowCount() > 0) {
                                // La tabla existe, obtener mensajes no leídos
                                $unread_count = $message_model->getUnreadCount($_SESSION['user_id']);
                                
                                // Obtener últimos mensajes no leídos para mostrar en notificaciones
                                $unread_messages = $message_model->getUnreadMessages($_SESSION['user_id'], 3);
                                foreach ($unread_messages as $msg) {
                                    $notifications[] = [
                                        'type' => 'message',
                                        'title' => $msg['sender_name'],
                                        'content' => (strlen($msg['content']) > 30) ? substr($msg['content'], 0, 30) . '...' : $msg['content'],
                                        'time' => $msg['created_at'],
                                        'url' => 'index.php?controller=chat&action=conversation&user_id=' . $msg['sender_id']
                                    ];
                                }
                            }
                        } catch (PDOException $e) {
                            // La tabla no existe o hay algún problema con la consulta
                            $unread_count = 0;
                        }
                    }
                    
                    // Aquí podrías añadir otras notificaciones del sistema
                } catch (Exception $e) {
                    // Si hay algún error, simplemente ignorar
                    $unread_count = 0;
                    $notifications = [];
                }
            }
            
            // Incluir el layout
            include_once "views/{$layout}.php";
        } catch (Exception $e) {
            // Si hay algún error crítico, mostrar un mensaje amigable
            echo "Ha ocurrido un error. Por favor, contacte al administrador.";
            // En entorno de desarrollo, mostrar el error
            if (true) { // Cambiar a una variable de configuración para entorno
                echo "<pre>Error: " . $e->getMessage() . "\n";
                echo $e->getTraceAsString() . "</pre>";
            }
        }
    }
}
?>