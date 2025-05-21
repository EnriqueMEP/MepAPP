<?php
class ChatController {
    private $db;
    private $user;
    
    public function __construct() {
        // Inicializar base de datos
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Inicializar modelo de usuario
        require_once 'models/user.php';
        $this->user = new User($this->db);
        
        // Verificar si el usuario está autenticado
        session_start();
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
    
    // Método principal - Vista del chat
    public function index() {
        // Obtener todos los usuarios para la lista de contactos
        $stmt = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrar el usuario actual de la lista
        $contacts = array_filter($users, function($user) {
            return $user['id'] != $_SESSION['user_id'];
        });
        
        // Simulación de chats recientes
        $chats_recientes = [
            [
                'user_id' => 2,
                'name' => 'Ana López',
                'message' => 'Hola, ¿cómo va el proyecto?',
                'time' => '10:30',
                'unread' => 2,
                'status' => 'online',
                'avatar' => 'AL'
            ],
            [
                'user_id' => 3,
                'name' => 'Carlos Gómez',
                'message' => 'Ya te envié los archivos por correo',
                'time' => 'Ayer',
                'unread' => 0,
                'status' => 'offline',
                'avatar' => 'CG'
            ],
            [
                'user_id' => 4,
                'name' => 'María Rodríguez',
                'message' => 'Necesitamos hablar del presupuesto',
                'time' => 'Lun',
                'unread' => 1,
                'status' => 'online',
                'avatar' => 'MR'
            ],
            [
                'user_id' => 5,
                'name' => 'Pedro Sánchez',
                'message' => 'Revisando los últimos cambios...',
                'time' => '24/04',
                'unread' => 0,
                'status' => 'offline',
                'avatar' => 'PS'
            ]
        ];
        
        // Simulación de mensajes para una conversación
        $active_chat = isset($_GET['user_id']) ? intval($_GET['user_id']) : 2;
        
        // Encontrar el chat activo
        $active_chat_user = null;
        foreach ($chats_recientes as $chat) {
            if ($chat['user_id'] == $active_chat) {
                $active_chat_user = $chat;
                break;
            }
        }
        
        if (!$active_chat_user && !empty($contacts)) {
            $contact = reset($contacts);
            $active_chat_user = [
                'user_id' => $contact['id'],
                'name' => $contact['full_name'],
                'status' => 'offline',
                'avatar' => implode('', array_map(function($n) { return strtoupper($n[0]); }, explode(' ', $contact['full_name'])))
            ];
        }
        
        // Mensajes de ejemplo para la conversación
        $mensajes = [
            [
                'user_id' => $active_chat,
                'message' => 'Hola, ¿cómo estás?',
                'time' => '10:15',
                'is_mine' => false
            ],
            [
                'user_id' => $_SESSION['user_id'],
                'message' => 'Hola! Todo bien, trabajando en el proyecto de MEP-2025. ¿Y tú?',
                'time' => '10:17',
                'is_mine' => true
            ],
            [
                'user_id' => $active_chat,
                'message' => 'También, estoy revisando los documentos que me enviaste ayer.',
                'time' => '10:20',
                'is_mine' => false
            ],
            [
                'user_id' => $_SESSION['user_id'],
                'message' => 'Perfecto. ¿Has podido revisar el apartado de presupuestos?',
                'time' => '10:22',
                'is_mine' => true
            ],
            [
                'user_id' => $active_chat,
                'message' => 'Sí, me parece correcto. Aunque tengo algunas dudas sobre la implementación del módulo CRM.',
                'time' => '10:25',
                'is_mine' => false
            ],
            [
                'user_id' => $_SESSION['user_id'],
                'message' => 'Claro, podemos agendar una reunión para discutirlo en detalle. ¿Te parece bien mañana a las 11:00?',
                'time' => '10:28',
                'is_mine' => true
            ],
            [
                'user_id' => $active_chat,
                'message' => 'Perfecto, me viene bien. Lo agendaré en mi calendario.',
                'time' => '10:30',
                'is_mine' => false
            ],
        ];
        
        // Título de la página
        $title = "Chat Interno";
        
        // Cargar la vista
        include_once 'views/chat/index.php';
    }
    
    // Método para enviar un mensaje (simulado)
    public function send() {
        // Verificar si se envió un mensaje
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
            $to_user_id = isset($_POST['to_user_id']) ? intval($_POST['to_user_id']) : 0;
            $message = trim($_POST['message']);
            
            // En una implementación real, aquí guardaríamos el mensaje en la base de datos
            
            // Redirigir de vuelta al chat con el usuario
            header("Location: index.php?controller=chat&user_id={$to_user_id}");
            exit;
        }
        
        // Si no se envió un mensaje, redirigir a la vista principal del chat
        header('Location: index.php?controller=chat');
        exit;
    }
    
    // Método para crear un nuevo chat (simulado)
    public function new_chat() {
        // Obtener todos los usuarios para seleccionar
        $stmt = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrar el usuario actual de la lista
        $contacts = array_filter($users, function($user) {
            return $user['id'] != $_SESSION['user_id'];
        });
        
        // Título de la página
        $title = "Nuevo Chat";
        
        // Cargar la vista
        include_once 'views/chat/new.php';
    }
}
?>