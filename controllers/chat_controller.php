<?php
// controllers/chat_controller.php
require_once 'controllers/base_controller.php';

class ChatController extends BaseController {
    private $message;
    
    public function __construct() {
        parent::__construct();
        
        // Cargar modelo de mensajes
        require_once 'models/message.php';
        $this->message = new Message($this->db);
    }
    
    public function index() {
        // Verificar permisos
        $this->requirePermission('chat', 'read');
        
        // Obtener todos los usuarios para la lista de chat
        require_once 'models/user.php';
        $userModel = new User($this->db);
        $users = $userModel->read()->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener conversaciones recientes
        $conversations = $this->message->getRecentConversations($_SESSION['user_id']);
        
        // Renderizar vista de chat
        $this->render('chat/index', [
            'title' => 'Chat',
            'users' => $users,
            'conversations' => $conversations,
            'current_user_id' => $_SESSION['user_id']
        ]);
    }
    
    public function conversation() {
        // Verificar permisos
        $this->requirePermission('chat', 'read');
        
        // Obtener ID de usuario de la consulta
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if(!$user_id) {
            header('Location: index.php?controller=chat');
            exit;
        }
        
        // Obtener detalles del usuario
        require_once 'models/user.php';
        $userModel = new User($this->db);
        $userModel->id = $user_id;
        $userModel->read_single();
        
        // Obtener mensajes entre usuarios
        $messages = $this->message->getConversation($_SESSION['user_id'], $user_id);
        
        // Marcar mensajes como leídos
        $this->message->markAsRead($user_id, $_SESSION['user_id']);
        
        // Obtener todos los usuarios para la lista de chat
        $users = $userModel->read()->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener conversaciones recientes
        $conversations = $this->message->getRecentConversations($_SESSION['user_id']);
        
        // Renderizar vista de conversación
        $this->render('chat/conversation', [
            'title' => 'Chat con ' . $userModel->full_name,
            'users' => $users,
            'conversations' => $conversations,
            'messages' => $messages,
            'chat_user' => [
                'id' => $userModel->id,
                'full_name' => $userModel->full_name,
                'email' => $userModel->email
            ],
            'current_user_id' => $_SESSION['user_id']
        ]);
    }
    
    public function send() {
        // Verificar permisos
        $this->requirePermission('chat', 'write');
        
        // Solo manejar peticiones POST
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=chat');
            exit;
        }
        
        // Verificar parámetros requeridos
        if(empty($_POST['recipient_id']) || empty($_POST['message'])) {
            header('Location: index.php?controller=chat');
            exit;
        }
        
        // Establecer propiedades del mensaje
        $this->message->sender_id = $_SESSION['user_id'];
        $this->message->recipient_id = intval($_POST['recipient_id']);
        $this->message->content = htmlspecialchars($_POST['message']);
        $this->message->created_at = date('Y-m-d H:i:s');
        $this->message->is_read = 0;
        
        // Guardar mensaje
        if($this->message->create()) {
            header('Location: index.php?controller=chat&action=conversation&user_id=' . $this->message->recipient_id);
        } else {
            // Mostrar error
            $error = "No se pudo enviar el mensaje.";
            header('Location: index.php?controller=chat&action=conversation&user_id=' . $this->message->recipient_id . '&error=' . urlencode($error));
        }
        exit;
    }
    
    // Endpoint AJAX para obtener nuevos mensajes
    public function check_new() {
        // Verificar permisos
        $this->requirePermission('chat', 'read');
        
        // Establecer cabecera de respuesta a JSON
        header('Content-Type: application/json');
        
        // Obtener último ID de mensaje del cliente
        $last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if($user_id) {
            // Obtener nuevos mensajes para la conversación
            $messages = $this->message->getNewMessages($_SESSION['user_id'], $user_id, $last_id);
            
            // Marcar como leídos
            $this->message->markAsRead($user_id, $_SESSION['user_id']);
        } else {
            // Verificar nuevos mensajes de cualquier usuario
            $messages = $this->message->getNewMessagesForUser($_SESSION['user_id'], $last_id);
        }
        
        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'unread_count' => $this->message->getUnreadCount($_SESSION['user_id'])
        ]);
        exit;
    }
}
?>