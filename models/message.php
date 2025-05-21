<?php
// models/message.php

class Message {
    private $conn;
    private $table = 'messages';
    
    public $id;
    public $chat_id;
    public $sender_id;
    public $content;
    public $created_at;
    
    public function __construct(PDO $db) {
        $this->conn = $db;
    }
    
    /**
     * Envía un mensaje vinculándolo a un chat existente.
     */
    public function sendMessage(int $chatId, int $senderId, string $content): bool {
        $sql = "
            INSERT INTO {$this->table}
              (chat_id, sender_id, content)
            VALUES
              (:chat_id, :sender_id, :content)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chat_id',   $chatId,     PDO::PARAM_INT);
        $stmt->bindParam(':sender_id', $senderId,   PDO::PARAM_INT);
        $stmt->bindParam(':content',   $content,    PDO::PARAM_STR);
        if ($stmt->execute()) {
            $this->id = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Obtiene todos los mensajes de una conversación (chat_id).
     */
    public function getConversation(int $chatId, int $limit = 100, int $offset = 0): array {
        $sql = "
            SELECT
              m.id,
              m.chat_id,
              m.sender_id,
              m.content,
              m.created_at,
              u.full_name AS sender_name
            FROM {$this->table} m
            JOIN users u ON u.id = m.sender_id
            WHERE m.chat_id = :chat_id
            ORDER BY m.created_at ASC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':chat_id', $chatId,    PDO::PARAM_INT);
        $stmt->bindParam(':limit',   $limit,     PDO::PARAM_INT);
        $stmt->bindParam(':offset',  $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene lista de conversaciones recientes para un usuario.
     * Retorna cada chat_id, el otro participante y hora del último mensaje.
     */
    public function getRecentConversations(int $userId): array {
        $sql = "
          SELECT
            p.id             AS chat_id,
            CASE
              WHEN p.user_one_id = :me THEN p.user_two_id
              ELSE p.user_one_id
            END               AS other_user_id,
            u.full_name       AS other_user_name,
            m.content         AS last_message,
            m.created_at      AS last_message_time
          FROM chats p
          JOIN (
            SELECT chat_id, MAX(created_at) AS maxt
            FROM {$this->table}
            GROUP BY chat_id
          ) lm ON lm.chat_id = p.id
          JOIN {$this->table} m
            ON m.chat_id = lm.chat_id
           AND m.created_at = lm.maxt
          JOIN users u ON u.id = CASE
            WHEN p.user_one_id = :me THEN p.user_two_id
            ELSE p.user_one_id
          END
          WHERE p.user_one_id = :me OR p.user_two_id = :me
          ORDER BY m.created_at DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':me', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
