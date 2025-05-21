<?php
// controllers/ChatController.php

require_once __DIR__ . '/base_controller.php';
require_once __DIR__ . '/../models/message.php';
require_once __DIR__ . '/../models/user.php';

class ChatController extends BaseController {
    private $messageModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->messageModel = new Message($this->db);
        $this->userModel    = new User($this->db);
    }

    /**
     * Muestra la lista de chats y abre el modal de "nueva conversación"
     */
    public function index() {
        // 1) Aseguramos que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        $me = (int) $_SESSION['user_id'];

        // 2) Conversaciones recientes
        $conversations = $this->messageModel->getRecentConversations($me);

        // 3) Lista de todos los usuarios (para el modal)
        $stmtUsers = $this->userModel->read();
        $users     = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        // 4) Renderizamos la vista, pasándole las variables
        $this->render('chat/index', [
            'title'            => 'Chat Interno',
            'conversations'    => $conversations,
            'users'            => $users,
            'current_user_id'  => $me,
        ]);
    }

    /**
     * Muestra la conversación con un usuario concreto
     */
    public function conversation() {
        $this->requirePermission('chat', 'read');
        $me = (int) $_SESSION['user_id'];
        $otherId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

        if (!$otherId) {
            header('Location: index.php?controller=chat');
            exit;
        }

        // 1) Obtener mensajes bidireccionales
        $mensajes = $this->messageModel->getConversation($me, $otherId);

        // 2) Marcar como leídos los que vienen de él
        $this->messageModel->markAsRead($otherId, $me);

        // 3) Recargar la lista de conversaciones laterales
        $conversations = $this->messageModel->getRecentConversations($me);

        // 4) Lista de usuarios (para modal)
        $users = $this->userModel->read()->fetchAll(PDO::FETCH_ASSOC);

        // 5) Nombre del otro usuario (opcional, para título)
        $this->userModel->id = $otherId;
        $this->userModel->read_single();
        $otherName = $this->userModel->full_name;

        // 6) Renderizamos la vista específica de conversación
        $this->render('chat/conversation', [
            'title'            => "Chat con {$otherName}",
            'conversations'    => $conversations,
            'users'            => $users,
            'current_user_id'  => $me,
            'chat_user_id'     => $otherId,
            'mensajes'         => $mensajes,
        ]);
    }

    /**
     * Procesa el envío de un mensaje (form POST)
     */
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $me      = (int) $_SESSION['user_id'];
            $other   = (int) ($_POST['user_id'] ?? 0);
            $content = trim($_POST['message'] ?? '');

            if ($other && $content !== '') {
                // Creamos el mensaje
                $this->messageModel->sender_id    = $me;
                $this->messageModel->recipient_id = $other;
                $this->messageModel->content      = $content;
                $this->messageModel->created_at   = date('Y-m-d H:i:s');
                $this->messageModel->is_read      = 0;
                $this->messageModel->create();
            }

            // Redirigimos de vuelta a la conversación
            header("Location: index.php?controller=chat&action=conversation&user_id={$other}");
            exit;
        }

        // Si alguien entra por GET aquí, lo mandamos al index de chat
        header('Location: index.php?controller=chat');
        exit;
    }

    /**
     * Endpoint AJAX para comprobar nuevos mensajes
     */
    public function check_new() {
        $this->requirePermission('chat', 'read');
        header('Content-Type: application/json');

        $lastId = isset($_GET['last_id']) ? (int) $_GET['last_id'] : 0;
        $me     = (int) $_SESSION['user_id'];
        $other  = isset($_GET['user_id'])   ? (int) $_GET['user_id']   : null;

        $response = [
            'success'      => false,
            'messages'     => [],
            'unread_count' => 0,
        ];

        if ($other !== null) {
            // Nuevos mensajes en esa conversación
            $response['messages'] = $this->messageModel->getNewMessages($me, $other, $lastId);
            $response['success']  = true;
        }

        // Recalculamos el total de no leídos
        $convos = $this->messageModel->getRecentConversations($me);
        $unread = 0;
        foreach ($convos as $c) {
            $unread += $c['unread_count'] ?? 0;
        }
        $response['unread_count'] = $unread;

        echo json_encode($response);
        exit;
    }
}
