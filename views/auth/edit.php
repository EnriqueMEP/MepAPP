<?php
// Título para el layout
$title = "Editar Usuario";

// Incluir header del layout
include_once 'views/layout_header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
            <p class="text-sm text-gray-600 mt-1">Modifica los datos del usuario</p>
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
    
    <?php if ($user_data): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="index.php?controller=auth&action=edit&id=<?php echo $user_data['id']; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Correo electrónico</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña (dejar en blanco para mantener)</label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nueva contraseña">
                    </div>
                    
                    <div>
                        <label for="full_name" class="block text-gray-700 font-medium mb-2">Nombre completo</label>
                        <input type="text" id="full_name" name="full_name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?php echo htmlspecialchars($user_data['full_name']); ?>">
                    </div>
                    
                    <div>
                        <label for="role" class="block text-gray-700 font-medium mb-2">Rol</label>
                        <select id="role" name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="admin" <?php echo $user_data['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                            <option value="manager" <?php echo $user_data['role'] === 'manager' ? 'selected' : ''; ?>>Gerente</option>
                            <option value="employee" <?php echo $user_data['role'] === 'employee' ? 'selected' : ''; ?>>Empleado</option>
                            <option value="client" <?php echo $user_data['role'] === 'client' ? 'selected' : ''; ?>>Cliente</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="department" class="block text-gray-700 font-medium mb-2">Departamento</label>
                        <input type="text" id="department" name="department"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?php echo htmlspecialchars($user_data['department']); ?>">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="active" name="active" <?php echo $user_data['active'] ? 'checked' : ''; ?>
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="active" class="ml-2 block text-gray-700 font-medium">
                            Usuario activo
                        </label>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
            <p>No se encontró el usuario solicitado.</p>
            <p class="mt-2"><a href="index.php?controller=auth&action=users" class="text-yellow-800 underline">Volver a la lista de usuarios</a></p>
        </div>
    <?php endif; ?>
</div>

<?php
// Incluir footer del layout
include_once 'views/layout_footer.php';
?>