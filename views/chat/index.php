<?php
// Incluir el layout header
include_once 'views/layout_header.php';
?>

<div class="flex flex-col h-screen pt-16 -mt-16">
    <div class="flex-1 flex">
        <!-- Sidebar de chats -->
        <div class="w-full sm:w-80 md:w-96 bg-white border-r border-gray-200 flex flex-col">
            <!-- Buscar y crear nuevo chat -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-800">Mensajes</h2>
                    <a href="index.php?controller=chat&action=new_chat" class="text-mep-primary hover:text-mep-primary-dark">
                        <i data-lucide="edit" class="w-5 h-5"></i>
                    </a>
                </div>
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Buscar chat..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border-mep-primary focus:ring-1 focus:ring-mep-primary focus:outline-none"
                    />
                    <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
                </div>
            </div>
            
            <!-- Lista de chats -->
            <div class="flex-1 overflow-y-auto">
                <?php foreach ($chats_recientes as $chat): ?>
                    <a 
                        href="index.php?controller=chat&user_id=<?php echo $chat['user_id']; ?>" 
                        class="flex items-center p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors <?php echo $active_chat == $chat['user_id'] ? 'bg-gray-50' : ''; ?>"
                    >
                        <div class="relative">
                            <div class="w-12 h-12 bg-mep-primary text-white rounded-full flex items-center justify-center text-sm font-medium">
                                <?php echo $chat['avatar']; ?>
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 <?php echo $chat['status'] === 'online' ? 'bg-green-500' : 'bg-gray-400'; ?> rounded-full border-2 border-white"></span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-medium text-gray-800"><?php echo $chat['name']; ?></h3>
                                <span class="text-xs text-gray-500"><?php echo $chat['time']; ?></span>
                            </div>
                            <p class="text-sm text-gray-600 truncate"><?php echo $chat['message']; ?></p>
                        </div>
                        <?php if ($chat['unread'] > 0): ?>
                            <div class="ml-2 bg-mep-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                <?php echo $chat['unread']; ?>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Contenido del chat -->
        <div class="hidden sm:flex flex-1 flex-col bg-gray-50">
            <?php if (isset($active_chat_user)): ?>
                <!-- Encabezado del chat -->
                <div class="bg-white p-4 border-b border-gray-200 flex items-center">
                    <div class="relative mr-3">
                        <div class="w-10 h-10 bg-mep-primary text-white rounded-full flex items-center justify-center text-sm font-medium">
                            <?php echo $active_chat_user['avatar']; ?>
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 <?php echo $active_chat_user['status'] === 'online' ? 'bg-green-500' : 'bg-gray-400'; ?> rounded-full border-2 border-white"></span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800"><?php echo $active_chat_user['name']; ?></h3>
                        <span class="text-xs text-gray-500">
                            <?php echo $active_chat_user['status'] === 'online' ? 'En línea' : 'Desconectado'; ?>
                        </span>
                    </div>
                    <div class="ml-auto flex items-center space-x-3">
                        <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </button>
                        <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i data-lucide="video" class="w-5 h-5"></i>
                        </button>
                        <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i data-lucide="more-vertical" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mensajes del chat -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                    <?php foreach ($mensajes as $mensaje): ?>
                        <div class="flex <?php echo $mensaje['is_mine'] ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-xs md:max-w-md lg:max-w-lg <?php echo $mensaje['is_mine'] ? 'bg-mep-primary text-white' : 'bg-white text-gray-800'; ?> rounded-lg p-3 shadow-sm">
                                <p class="text-sm"><?php echo $mensaje['message']; ?></p>
                                <span class="text-xs <?php echo $mensaje['is_mine'] ? 'text-green-100' : 'text-gray-500'; ?> block text-right mt-1">
                                    <?php echo $mensaje['time']; ?>
                                    <?php if ($mensaje['is_mine']): ?>
                                        <i data-lucide="check" class="w-3 h-3 inline-block ml-1"></i>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Enviar mensaje -->
                <div class="bg-white p-4 border-t border-gray-200">
                    <form action="index.php?controller=chat&action=send" method="POST" class="flex items-center space-x-2">
                        <input type="hidden" name="to_user_id" value="<?php echo $active_chat_user['user_id']; ?>">
                        <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i data-lucide="paperclip" class="w-5 h-5"></i>
                        </button>
                        <input 
                            type="text" 
                            name="message"
                            placeholder="Escribe un mensaje..." 
                            class="flex-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:border-mep-primary focus:ring-1 focus:ring-mep-primary"
                        />
                        <button type="submit" class="bg-mep-primary text-white rounded-lg p-2 hover:bg-mep-primary-dark transition-colors">
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- No hay chat seleccionado -->
                <div class="flex-1 flex flex-col items-center justify-center">
                    <div class="bg-mep-primary bg-opacity-10 p-6 rounded-full mb-4">
                        <i data-lucide="message-circle" class="w-12 h-12 text-mep-primary"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">No hay conversación seleccionada</h3>
                    <p class="text-sm text-gray-600 mb-4">Selecciona un chat de la lista o inicia una nueva conversación</p>
                    <a href="index.php?controller=chat&action=new_chat" class="bg-mep-primary text-white py-2 px-4 rounded-lg hover:bg-mep-primary-dark transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 inline-block mr-1"></i>
                        Nuevo Chat
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Vista móvil - Mensaje de selección -->
        <div class="flex-1 flex flex-col items-center justify-center sm:hidden">
            <div class="bg-mep-primary bg-opacity-10 p-6 rounded-full mb-4">
                <i data-lucide="message-circle" class="w-12 h-12 text-mep-primary"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">Bandeja de Mensajes</h3>
            <p class="text-sm text-gray-600 mb-4">Selecciona un chat para ver la conversación</p>
        </div>
    </div>
</div>

<!-- Script para desplazar automáticamente hasta el último mensaje -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>

<?php
// El layout_footer.php se incluye automáticamente
include_once 'views/layout_footer.php';
?>