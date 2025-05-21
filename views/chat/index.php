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
                       class="flex items-center p-3 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-medium">
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
        <div class="p-4 flex items-center justify-center h-full">
            <div class="text-center text-gray-500">
                <i data-lucide="message-circle" class="w-16 h-16 mx-auto text-gray-300"></i>
                <p class="mt-4 text-lg font-medium">Selecciona una conversación</p>
                <p class="mt-2">O inicia una nueva para comenzar a chatear</p>
                <button id="new-conversation-btn" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg">
                    Nueva conversación
                </button>
            </div>
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
        // Modal de nueva conversación
        const newConversationBtn = document.getElementById('new-conversation-btn');
        const startNewChatLink = document.getElementById('start-new-chat');
        const newConversationModal = document.getElementById('new-conversation-modal');
        const closeModal = document.getElementById('close-modal');
        const searchUsers = document.getElementById('search-users');
        const usersList = document.getElementById('users-list');
        
        // Mostrar modal
        if (newConversationBtn) {
            newConversationBtn.addEventListener('click', function() {
                newConversationModal.classList.remove('hidden');
            });
        }
        
        if (startNewChatLink) {
            startNewChatLink.addEventListener('click', function(e) {
                e.preventDefault();
                newConversationModal.classList.remove('hidden');
            });
        }
        
        // Cerrar modal
        if (closeModal) {
            closeModal.addEventListener('click', function() {
                newConversationModal.classList.add('hidden');
            });
        }
        
        // Búsqueda de usuarios
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
        
        // Búsqueda de chats
        const searchChat = document.getElementById('search-chat');
        const conversationList = document.getElementById('conversation-list');
        
        if (searchChat) {
            searchChat.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const chatItems = conversationList.querySelectorAll('a');
                
                chatItems.forEach(item => {
                    const userName = item.querySelector('.font-medium').textContent.toLowerCase();
                    
                    if (userName.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });
</script>