<?php 
// views/auth/login.php

// Título para el layout
$title = "Iniciar Sesión";

// Iniciar buffer de salida
ob_start();
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-blue-800">MEP-Projects</h1>
            <p class="text-gray-600">Inicia sesión en tu cuenta</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?controller=auth&action=login">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Correo electrónico</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="tu@email.com">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="••••••••">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>¿Olvidaste tu contraseña? Contacta con un administrador</p>
        </div>
    </div>
</div>

<?php
// Obtener el contenido del buffer
$content = ob_get_clean();

// Incluir el layout de autenticación
include_once __DIR__ . '/../layout_auth.php';