<?php
// models/message.php
class Message {
    private $conn;
    private $table = 'messages';
    
    // Propiedades del mensaje
    public $id;
    public $sender_id;
    public $recipient_id;
    public $content;
    public $created_at;
    public $is_read;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Crear un nuevo mensaje
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                 (sender_id, recipient_id, content, created_at, is_read)
                 VALUES
                 (:sender_id, :recipient_id, :content, :created_at, :is_read)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar entradas
        $this->sender_id = htmlspecialchars(strip_tags($this->sender_id));
        $this->recipient_id = htmlspecialchars(strip_tags($this->recipient_id));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->is_read = htmlspecialchars(strip_tags($this->is_read));
        
        // Vincular parámetros
        $stmt->bindParam(':sender_id', $this->sender_id);
        $stmt->bindParam(':recipient_id', $this->recipient_id);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':is_read', $this->is_read);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Obtener conversación entre dos usuarios
    public function getConversation($user1, $user2) {
        $query = "SELECT m.*, u.full_name as sender_name, u.email as sender_email
                 FROM " . $this->table . " m
                 JOIN users u ON m.sender_id = u.id
                 WHERE (m.sender_id = :user1 AND m.recipient_id = :user2)
                    OR (m.sender_id = :user2 AND m.recipient_id = :user1)
                 ORDER BY m.created_at ASC";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user1', $user1);
        $stmt->bindParam(':user2', $user2);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener conversaciones recientes para un usuario
    public function getRecentConversations($user_id) {
        $query = "SELECT 
                    u.id, 
                    u.full_name, 
                    u.email,
                    MAX(m.created_at) as last_message_time,
                    (SELECT COUNT(*) FROM " . $this->table . " 
                     WHERE sender_id = u.id AND recipient_id = :user_id AND is_read = 0) as unread_count
                 FROM users u
                 JOIN " . $this->table . " m ON (m.sender_id = u.id AND m.recipient_id = :user_id)
                                         OR (m.recipient_id = u.id AND m.sender_id = :user_id)
                 WHERE u.id != :user_id
                 GROUP BY u.id
                 ORDER BY last_message_time DESC";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Marcar mensajes como leídos
    public function markAsRead($sender_id, $recipient_id) {
        $query = "UPDATE " . $this->table . "
                 SET is_read = 1
                 WHERE sender_id = :sender_id AND recipient_id = :recipient_id AND is_read = 0";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->bindParam(':recipient_id', $recipient_id);
        
        return $stmt->execute();
    }
    
    // Obtener nuevos mensajes después de un cierto ID
    public function getNewMessages($user1, $user2, $last_id) {
        $query = "SELECT m.*, u.full_name as sender_name, u.email as sender_email
                 FROM " . $this->table . " m
                 JOIN users u ON m.sender_id = u.id
                 WHERE ((m.sender_id = :user1 AND m.recipient_id = :user2)
                    OR (m.sender_id = :user2 AND m.recipient_id = :user1))
                    AND m.id > :last_id
                 ORDER BY m.created_at ASC";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user1', $user1);
        $stmt->bindParam(':user2', $user2);
        $stmt->bindParam(':last_id', $last_id);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener nuevos mensajes para un usuario
    public function getNewMessagesForUser($user_id, $last_id) {
        $query = "SELECT m.*, u.full_name as sender_name, u.email as sender_email
                 FROM " . $this->table . " m
                 JOIN users u ON m.sender_id = u.id
                 WHERE m.recipient_id = :user_id
                    AND m.id > :last_id
                    AND m.is_read = 0
                 ORDER BY m.created_at ASC";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':last_id', $last_id);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener cantidad de mensajes no leídos
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as count
                 FROM " . $this->table . "
                 WHERE recipient_id = :user_id AND is_read = 0";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    // Obtener mensajes no leídos para un usuario
    public function getUnreadMessages($user_id, $limit = 5) {
        $query = "SELECT m.*, u.full_name as sender_name, u.email as sender_email
                 FROM " . $this->table . " m
                 JOIN users u ON m.sender_id = u.id
                 WHERE m.recipient_id = :user_id
                    AND m.is_read = 0
                 ORDER BY m.created_at DESC
                 LIMIT :limit";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener mensajes de un chat
    public function getMessages($chat_id, $limit = 50, $offset = 0) {
        $query = "SELECT m.*, u.full_name as sender_name, u.email as sender_email
                 FROM " . $this->table . " m
                 JOIN users u ON m.sender_id = u.id
                 WHERE m.chat_id = :chat_id
                 ORDER BY m.created_at DESC
                 LIMIT :limit OFFSET :offset";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>