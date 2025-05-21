<?php
class Chat {
    private $conn;
    
    // Propiedades
    public $conversation_id;
    public $user_id;
    public $message;
    public $has_attachment;
    public $attachment_type;
    public $attachment_url;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Obtener conversaciones recientes de un usuario
    public function get_recent_chats() {
        $query = "CALL GetRecentChats(?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Obtener mensajes de una conversación
    public function get_messages() {
        $query = "CALL GetConversationMessages(?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->bindParam(2, $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Enviar un mensaje
    public function send_message() {
        $query = "CALL SendMessage(?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Valores predeterminados para adjuntos
        $has_attachment = $this->has_attachment ?? false;
        $attachment_type = $this->attachment_type ?? null;
        $attachment_url = $this->attachment_url ?? null;
        
        // Binding
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->bindParam(2, $this->user_id);
        $stmt->bindParam(3, $this->message);
        $stmt->bindParam(4, $has_attachment);
        $stmt->bindParam(5, $attachment_type);
        $stmt->bindParam(6, $attachment_url);
        
        // Ejecutar
        $stmt->execute();
        
        // Obtener el ID del mensaje creado
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['message_id'] ?? false;
    }
    
    // Crear una nueva conversación
    public function create_conversation($is_group, $name, $participants) {
        $query = "CALL CreateConversation(?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Binding
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $is_group);
        $stmt->bindParam(3, $name);
        $stmt->bindParam(4, $participants);
        
        // Ejecutar
        $stmt->execute();
        
        // Obtener el ID de la conversación creada
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['conversation_id'] ?? false;
    }
    
    // Obtener detalles de una conversación
    public function get_conversation_details() {
        $query = "SELECT 
                    cc.id, 
                    cc.name as conversation_name, 
                    cc.is_group, 
                    cc.created_by,
                    cc.created_at,
                    u.full_name as creator_name,
                    (
                        SELECT COUNT(*) 
                        FROM chat_participants 
                        WHERE conversation_id = cc.id
                    ) as participant_count
                 FROM chat_conversations cc
                 JOIN users u ON cc.created_by = u.id
                 WHERE cc.id = ? LIMIT 1";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener participantes de una conversación
    public function get_participants() {
        $query = "SELECT 
                    u.id, 
                    u.full_name, 
                    u.email,
                    cp.is_admin,
                    cp.last_read_at,
                    us.status as user_status
                 FROM chat_participants cp
                 JOIN users u ON cp.user_id = u.id
                 LEFT JOIN user_status us ON u.id = us.user_id
                 WHERE cp.conversation_id = ?
                 ORDER BY cp.is_admin DESC, u.full_name";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Agregar un participante a una conversación
    public function add_participant($participant_id, $is_admin = false) {
        // Verificar si el participante ya está en la conversación
        $check_query = "SELECT id FROM chat_participants 
                        WHERE conversation_id = ? AND user_id = ? LIMIT 1";
                        
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->conversation_id);
        $check_stmt->bindParam(2, $participant_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            return false; // El participante ya está en la conversación
        }
        
        // Agregar el participante
        $query = "INSERT INTO chat_participants (conversation_id, user_id, is_admin) 
                  VALUES (?, ?, ?)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->bindParam(2, $participant_id);
        $stmt->bindParam(3, $is_admin);
        
        // Ejecutar
        if ($stmt->execute()) {
            // Añadir mensaje del sistema
            $system_message = "Se ha añadido a " . $this->get_user_name($participant_id) . " a la conversación";
            $this->add_system_message($system_message);
            return true;
        }
        
        return false;
    }
    
    // Eliminar un participante de una conversación
    public function remove_participant($participant_id) {
        // Verificar si es el creador (no se puede eliminar al creador)
        $check_query = "SELECT created_by FROM chat_conversations WHERE id = ? LIMIT 1";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->conversation_id);
        $check_stmt->execute();
        
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['created_by'] == $participant_id) {
            return false; // No se puede eliminar al creador
        }
        
        // Eliminar el participante
        $query = "DELETE FROM chat_participants
                  WHERE conversation_id = ? AND user_id = ?";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->bindParam(2, $participant_id);
        
        // Ejecutar
        if ($stmt->execute()) {
            // Añadir mensaje del sistema
            $system_message = $this->get_user_name($participant_id) . " ha sido eliminado de la conversación";
            $this->add_system_message($system_message);
            return true;
        }
        
        return false;
    }
    
    // Obtener el nombre de un usuario por su ID
    private function get_user_name($user_id) {
        $query = "SELECT full_name FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['full_name'] : 'Usuario desconocido';
    }
    
    // Añadir un mensaje del sistema a una conversación
    private function add_system_message($message) {
        $query = "INSERT INTO chat_messages (conversation_id, user_id, message, is_system_message)
                  VALUES (?, ?, ?, TRUE)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->bindParam(2, $this->user_id);
        $stmt->bindParam(3, $message);
        
        return $stmt->execute();
    }
    
    // Marcar todos los mensajes como leídos
    public function mark_all_as_read() {
        $query = "UPDATE chat_message_status cms
                  JOIN chat_messages cm ON cms.message_id = cm.id
                  SET cms.is_read = TRUE, cms.read_at = NOW()
                  WHERE cm.conversation_id = ? 
                  AND cms.user_id = ?
                  AND cms.is_read = FALSE";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->conversation_id);
        $stmt->bindParam(2, $this->user_id);
        
        return $stmt->execute();
    }
    
    // Obtener el número total de mensajes no leídos para un usuario
    public function get_unread_count() {
        $query = "SELECT COUNT(*) as unread_count
                  FROM chat_message_status cms
                  JOIN chat_messages cm ON cms.message_id = cm.id
                  JOIN chat_participants cp ON cm.conversation_id = cp.conversation_id
                  WHERE cp.user_id = ?
                  AND cms.user_id = ?
                  AND cms.is_read = FALSE";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['unread_count'] : 0;
    }
}