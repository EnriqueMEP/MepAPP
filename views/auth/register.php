<?php 
// Título para el layout
$title = "Registrar Usuario";

// Incluir header del layout
include_once 'views/layout_header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registrar Nuevo Usuario</h1>
            <p class="text-sm text-gray-600 mt-1">Crea una nueva cuenta para acceder al sistema</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="index.php?controller=auth&action=users" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a usuarios
            </a>
        </div>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="index.php?controller=auth&action=register">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-2">Correo electrónico</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="usuario@mep-projects.com">
                </div>
                
                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>
                
                <div>
                    <label for="full_name" class="block text-gray-700 font-medium mb-2">Nombre completo</label>
                    <input type="text" id="full_name" name="full_name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Juan Pérez">
                </div>
                
                <div>
                    <label for="role" class="block text-gray-700 font-medium mb-2">Rol</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar rol</option>
                        <option value="admin">Administrador</option>
                        <option value="manager">Gerente</option>
                        <option value="employee">Empleado</option>
                        <option value="client">Cliente</option>
                    </select>
                </div>
                
                <div>
                    <label for="department" class="block text-gray-700 font-medium mb-2">Departamento</label>
                    <input type="text" id="department" name="department"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Tecnología">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                    Registrar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Incluir footer del layout
include_once 'views/layout_footer.php';
?>