<?php
// Incluir el layout header
include_once 'views/layout_header.php';
?>

<div class="p-6 max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center">
                <a href="index.php?controller=chat" class="text-mep-primary hover:text-mep-primary-dark mr-2">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Nuevo Chat</h1>
            </div>
            <p class="text-sm text-gray-600 mt-1">Inicia una nueva conversación con un usuario</p>
        </div>
    </div>
    
    <!-- Buscador de contactos -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 border-b border-gray-200">
            <div class="relative">
                <input 
                    type="text" 
                    id="search-contacts"
                    placeholder="Buscar contacto..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border-mep-primary focus:ring-1 focus:ring-mep-primary focus:outline-none"
                />
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
        </div>
        
        <!-- Lista de contactos -->
        <div class="p-4">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Contactos</h3>
            
            <div class="space-y-2 max-h-96 overflow-y-auto" id="contact-list">
                <?php if (empty($contacts)): ?>
                    <p class="text-center text-gray-500 py-4">No hay contactos disponibles</p>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <a 
                            href="index.php?controller=chat&user_id=<?php echo $contact['id']; ?>" 
                            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div class="w-10 h-10 bg-mep-primary text-white rounded-full flex items-center justify-center text-sm font-medium">
                                <?php 
                                    $initials = implode('', array_map(function($n) { 
                                        return strtoupper($n[0]); 
                                    }, explode(' ', $contact['full_name'])));
                                    echo $initials;
                                ?>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-800"><?php echo $contact['full_name']; ?></h4>
                                <p class="text-xs text-gray-500"><?php echo $contact['email']; ?></p>
                            </div>
                            <div class="ml-auto">
                                <button class="text-mep-primary hover:text-mep-primary-dark focus:outline-none">
                                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Crear grupo de chat -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-medium text-gray-800">Crear un grupo</h3>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-600 mb-4">Crea un nuevo grupo para comunicarte con varios usuarios a la vez.</p>
            <button class="btn-primary flex items-center">
                <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                Crear grupo de chat
            </button>
        </div>
    </div>
</div>

<!-- Script para filtrar contactos -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-contacts');
        const contactList = document.getElementById('contact-list');
        const contacts = contactList.querySelectorAll('a');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            contacts.forEach(contact => {
                const name = contact.querySelector('h4').textContent.toLowerCase();
                const email = contact.querySelector('p').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    contact.style.display = 'flex';
                } else {
                    contact.style.display = 'none';
                }
            });
        });
    });
</script>

<?php
// Incluir el footer del layout
include_once 'views/layout_footer.php';
?>