<div class="flex h-screen bg-gray-100">
    <!-- Chat sidebar -->
    <div class="w-1/4 bg-white border-r border-gray-300 flex flex-col">
        <div class="p-4 border-b border-gray-300">
            <h2 class="text-lg font-semibold">Mensajes</h2>
            <div class="relative mt-2">
                <input type="text" id="search-chat" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg" placeholder="Buscar chat...">
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <!-- Lista de conversaciones -->
            <div id="conversation-list">
                <?php foreach ($conversations as $conversation): ?>
                    <a href="index.php?controller=chat&action=conversation&user_id=<?php echo $conversation['id']; ?>" 
                       class="flex items-center p-3 border-b border-gray-200 hover:bg-gray-50 transition-colors <?php echo isset($_GET['user_id']) && $_GET['user_id'] == $conversation['id'] ? 'bg-green-50' : ''; ?>">
                        <div class="w-10 h-10 rounded-full bg-<?php echo isset($_GET['user_id']) && $_GET['user_id'] == $conversation['id'] ? 'green' : 'gray'; ?>-500 flex items-center justify-center text-white font-medium">
                            <?php echo substr($conversation['full_name'], 0, 2); ?>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between items-baseline">
                                <span class="font-medium text-gray-900"><?php echo $conversation['full_name']; ?></span>
                                <span class="text-xs text-gray-500">
                                    <?php echo date('H:i', strtotime($conversation['last_message_time'])); ?>
                                </span>
                            </div>
                            <?php if ($conversation['unread_count'] > 0): ?>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-sm text-gray-500 truncate">Nuevos mensajes</span>
                                    <span class="bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        <?php echo $conversation['unread_count']; ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="text-sm text-gray-500 truncate">Sin mensajes nuevos</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
                
                <?php if (empty($conversations)): ?>
                    <div class="p-4 text-center text-gray-500">
                        No hay conversaciones recientes.
                        <p class="mt-2">
                            <a href="#" id="start-new-chat" class="text-green-600 hover:underline">Iniciar una nueva conversación</a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Contenido del chat -->
    <div class="flex-1 flex flex-col bg-gray-50">
        <!-- Cabecera de la conversación -->
        <div class="p-4 bg-white border-b border-gray-300 flex items-center shadow-sm">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-medium">
                <?php echo substr($chat_user['full_name'], 0, 2); ?>
            </div>
            <div class="ml-3">
                <h2 class="font-semibold text-gray-900"><?php echo $chat_user['full_name']; ?></h2>
                <span class="text-xs text-green-600">En línea</span>
            </div>
            <div class="ml-auto flex">
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <i data-lucide="phone" class="w-5 h-5 text-gray-600"></i>
                </button>
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <i data-lucide="video" class="w-5 h-5 text-gray-600"></i>
                </button>
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <i data-lucide="more-vertical" class="w-5 h-5 text-gray-600"></i>
                </button>
            </div>
        </div>
        
        <!-- Mensajes -->
        <div class="flex-1 overflow-y-auto p-4" id="messages-container">
            <?php
            $last_date = '';
            foreach ($messages as $message):
                $message_date = date('Y-m-d', strtotime($message['created_at']));
                if ($message_date != $last_date):
                    $last_date = $message_date;
                    $date_display = '';
                    
                    // Determinar cómo mostrar la fecha
                    $today = date('Y-m-d');
                    $yesterday = date('Y-m-d', strtotime('-1 day'));
                    
                    if ($message_date == $today) {
                        $date_display = 'Hoy';
                    } elseif ($message_date == $yesterday) {
                        $date_display = 'Ayer';
                    } else {
                        $date_display = date('d/m/Y', strtotime($message_date));
                    }
            ?>
                <div class="flex justify-center mb-4">
                    <div class="bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-full">
                        <?php echo $date_display; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="mb-4 <?php echo $message['sender_id'] == $current_user_id ? 'text-right' : ''; ?>">
                <div class="inline-block max-w-3/4 <?php echo $message['sender_id'] == $current_user_id ? 'bg-green-600 text-white' : 'bg-white text-gray-800'; ?> rounded-lg px-4 py-2 shadow-sm">
                    <?php echo nl2br($message['content']); ?>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    <?php echo date('H:i', strtotime($message['created_at'])); ?>
                    <?php if ($message['sender_id'] == $current_user_id && $message['is_read']): ?>
                        <i data-lucide="check-check" class="inline w-4 h-4 text-green-500"></i>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($messages)): ?>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center text-gray-500">
                        <i data-lucide="message-circle" class="w-16 h-16 mx-auto text-gray-300"></i>
                        <p class="mt-4 text-lg font-medium">No hay mensajes aún</p>
                        <p class="mt-2">¡Envía el primer mensaje a <?php echo $chat_user['full_name']; ?>!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Formulario de mensaje -->
        <div class="p-4 bg-white border-t border-gray-300">
            <form id="message-form" method="POST" action="index.php?controller=chat&action=send" class="flex items-center">
                <input type="hidden" name="recipient_id" value="<?php echo $chat_user['id']; ?>">
                
                <button type="button" class="p-2 rounded-full hover:bg-gray-100">
                    <i data-lucide="paperclip" class="w-5 h-5 text-gray-600"></i>
                </button>
                
                <input type="text" name="message" id="message-input" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 mx-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Escribe un mensaje...">
                
                <button type="submit" class="p-2 bg-green-600 rounded-full hover:bg-green-700 transition-colors">
                    <i data-lucide="send" class="w-5 h-5 text-white"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de nueva conversación -->
<div id="new-conversation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Nueva conversación</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-4">
            <div class="mb-4">
                <input type="text" id="search-users" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg" placeholder="Buscar usuario...">
            </div>
            <div class="max-h-64 overflow-y-auto" id="users-list">
                <?php foreach ($users as $user): ?>
                    <?php if ($user['id'] != $current_user_id): ?>
                        <a href="index.php?controller=chat&action=conversation&user_id=<?php echo $user['id']; ?>" 
                           class="flex items-center p-3 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-medium">
                                <?php echo substr($user['full_name'], 0, 2); ?>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900"><?php echo $user['full_name']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $user['email']; ?></div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencia al contenedor de mensajes
        const messagesContainer = document.getElementById('messages-container');
        
        // Desplazar al final de los mensajes
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Enviar mensaje con Enter
        const messageInput = document.getElementById('message-input');
        const messageForm = document.getElementById('message-form');
        
        if (messageInput && messageForm) {
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (messageInput.value.trim() !== '') {
                        messageForm.submit();
                    }
                }
            });
        }
        
        // Modal de nueva conversación
        const startNewChatLink = document.getElementById('start-new-chat');
        const newConversationModal = document.getElementById('new-conversation-modal');
        const closeModal = document.getElementById('close-modal');
        const searchUsers = document.getElementById('search-users');
        const usersList = document.getElementById('users-list');
        
        if (startNewChatLink) {
            startNewChatLink.addEventListener('click', function(e) {
                e.preventDefault();
                newConversationModal.classList.remove('hidden');
            });
        }
        
        if (closeModal) {
            closeModal.addEventListener('click', function() {
                newConversationModal.classList.add('hidden');
            });
        }
        
        if (searchUsers) {
            searchUsers.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const userItems = usersList.querySelectorAll('a');
                
                userItems.forEach(item => {
                    const userName = item.querySelector('.font-medium').textContent.toLowerCase();
                    const userEmail = item.querySelector('.text-sm').textContent.toLowerCase();
                    
                    if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        
        // Comprobar nuevos mensajes periódicamente
        let lastMessageId = <?php echo !empty($messages) ? end($messages)['id'] : 0; ?>;
        const userId = <?php echo $chat_user['id']; ?>;
        
        function checkNewMessages() {
            fetch(`index.php?controller=chat&action=check_new&last_id=${lastMessageId}&user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        // Procesar nuevos mensajes
                        data.messages.forEach(message => {
                            // Actualizar ID del último mensaje
                            lastMessageId = Math.max(lastMessageId, message.id);
                            
                            // Crear elemento de mensaje
                            const messageDiv = document.createElement('div');
                            messageDiv.className = `mb-4 ${message.sender_id == <?php echo $current_user_id; ?> ? 'text-right' : ''}`;
                            
                            const messageContent = document.createElement('div');
                            messageContent.className = `inline-block max-w-3/4 ${message.sender_id == <?php echo $current_user_id; ?> ? 'bg-green-600 text-white' : 'bg-white text-gray-800'} rounded-lg px-4 py-2 shadow-sm`;
                            messageContent.innerHTML = message.content.replace(/\n/g, '<br>');
                            
                            const messageTime = document.createElement('div');
                            messageTime.className = 'text-xs text-gray-500 mt-1';
                            
                            // Formatear hora
                            const date = new Date(message.created_at);
                            messageTime.textContent = `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                            
                            messageDiv.appendChild(messageContent);
                            messageDiv.appendChild(messageTime);
                            
                            // Añadir mensaje al contenedor
                            messagesContainer.appendChild(messageDiv);
                            
                            // Desplazar al final
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        });
                    }
                })
                .catch(error => console.error('Error checking new messages:', error));
        }
        
        // Comprobar cada 5 segundos
        setInterval(checkNewMessages, 5000);
    });
</script>