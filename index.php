<?php
// Inicializar la aplicación
session_start();

// Incluir archivos de configuración
include_once "config/database.php";

// Inicializar variables de ruta
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verificar si se solicitó un controlador específico
if (!empty($controller)) {
    // Ruta al archivo del controlador
    $controller_file = "controllers/{$controller}_controller.php";
    
    // Verificar si el archivo del controlador existe
    if (file_exists($controller_file)) {
        include_once $controller_file;
        
        // Crear nombre de la clase del controlador
        $controller_class = ucfirst($controller) . "Controller";
        
        // Verificar si la clase existe
        if (class_exists($controller_class)) {
            $controller_obj = new $controller_class();
            
            // Verificar si el método solicitado existe
            if (method_exists($controller_obj, $action)) {
                // Ejecutar el método del controlador
                $controller_obj->$action();
                exit;
            } else {
                // Método no encontrado, cargar página de error
                include_once "views/404.php";
                exit;
            }
        } else {
            // Clase no encontrada, cargar página de error
            include_once "views/404.php";
            exit;
        }
    } else {
        // Archivo no encontrado, cargar página de error
        include_once "views/404.php";
        exit;
    }
}

// Si no se solicitó ningún controlador, mostrar la página de bienvenida
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP-Projects | Sistema de Gestión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header/Navbar -->
    <header class="bg-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <div class="text-xl font-bold">MEP-Projects</div>
                    <nav class="hidden md:flex space-x-6">
                        <a href="index.php?controller=dashboard" class="hover:text-blue-200">Dashboard</a>
                        <a href="index.php?controller=projects" class="hover:text-blue-200">Proyectos</a>
                        <a href="index.php?controller=crm" class="hover:text-blue-200">CRM</a>
                        <a href="index.php?controller=erp" class="hover:text-blue-200">ERP</a>
                        <a href="index.php?controller=rrhh" class="hover:text-blue-200">RRHH</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-700 rounded-md px-3 py-1 flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-blue-800 font-semibold">EG</div>
                        <span class="hidden md:inline">Enrique</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Bienvenido a MEP-Projects</h1>
            
            <div class="bg-white rounded-lg shadow p-6">
                <p class="mb-4">Este es el sistema de gestión empresarial para MEP-Projects. Desde aquí podrás administrar:</p>
                
                <ul class="list-disc pl-5 mb-6 space-y-2">
                    <li>Proyectos y tareas</li>
                    <li>Clientes y contactos (CRM)</li>
                    <li>Facturación y finanzas (ERP)</li>
                    <li>Recursos humanos</li>
                </ul>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="index.php?controller=dashboard" class="block p-4 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                        <h3 class="font-bold text-blue-800 mb-2">Dashboard</h3>
                        <p class="text-sm text-gray-600">Vista general de todos los módulos</p>
                    </a>
                    
                    <a href="index.php?controller=projects" class="block p-4 bg-green-50 rounded-lg border border-green-100 hover:bg-green-100 transition-colors">
                        <h3 class="font-bold text-green-800 mb-2">Proyectos</h3>
                        <p class="text-sm text-gray-600">Gestiona proyectos y tareas</p>
                    </a>
                    
                    <a href="index.php?controller=crm" class="block p-4 bg-purple-50 rounded-lg border border-purple-100 hover:bg-purple-100 transition-colors">
                        <h3 class="font-bold text-purple-800 mb-2">CRM</h3>
                        <p class="text-sm text-gray-600">Clientes y oportunidades</p>
                    </a>
                    
                    <a href="index.php?controller=rrhh" class="block p-4 bg-yellow-50 rounded-lg border border-yellow-100 hover:bg-yellow-100 transition-colors">
                        <h3 class="font-bold text-yellow-800 mb-2">RRHH</h3>
                        <p class="text-sm text-gray-600">Gestión de personal</p>
                    </a>
                </div>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-500 text-sm">Para continuar con la configuración, puede <a href="config/setup.php" class="text-blue-600 hover:underline">inicializar la base de datos</a>.</p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white py-4 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-500">
                    © <?php echo date('Y'); ?> MEP-Projects. Todos los derechos reservados.
                </div>
                <div class="text-sm text-gray-500 mt-2 md:mt-0">
                    v1.0.0 | <a href="#" class="text-blue-600">Soporte</a> | <a href="#" class="text-blue-600">Documentación</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
    <script src="assets/js/main.js"></script>

    <?php
// Iniciar la sesión
session_start();

// Incluir archivos de configuración
include_once "config/database.php";

// Inicializar variables de ruta
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verificar si el usuario está autenticado
$public_routes = ['auth.login', 'auth.logout'];
$route = $controller . '.' . $action;

if (!isset($_SESSION['user_id']) && !in_array($route, $public_routes) && $controller !== '') {
    // Usuario no autenticado, redirigir al login
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Si no se especificó ningún controlador pero el usuario está autenticado, ir al dashboard
if (empty($controller) && isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard');
    exit;
}

// Si no se especificó controlador y el usuario NO está autenticado, mostrar pantalla de inicio o ir al login
if (empty($controller) && !isset($_SESSION['user_id'])) {
    // Puedes optar por mostrar una pantalla de bienvenida o redirigir al login directamente
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Verificar si el controlador existe
$controller_file = "controllers/{$controller}_controller.php";

if (file_exists($controller_file)) {
    include_once $controller_file;
    
    // Crear el nombre de la clase del controlador
    $controller_class = ucfirst($controller) . "Controller";
    
    if (class_exists($controller_class)) {
        $controller_obj = new $controller_class();
        
        // Verificar si el método existe
        if (method_exists($controller_obj, $action)) {
            // Ejecutar el método del controlador
            $controller_obj->$action();
        } else {
            // Método no encontrado, cargar página de error
            include_once "views/404.php";
        }
    } else {
        // Clase no encontrada, cargar página de error
        include_once "views/404.php";
    }
} else {
    // Controlador no encontrado, cargar página de error
    include_once "views/404.php";
}
?>
<?php
// Iniciar la sesión
session_start();

// Incluir archivos de configuración
include_once "config/database.php";

// Inicializar variables de ruta
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verificar si el usuario está autenticado
$public_routes = ['auth.login', 'auth.logout'];
$route = $controller . '.' . $action;

if (!isset($_SESSION['user_id']) && !in_array($route, $public_routes) && $controller !== '') {
    // Usuario no autenticado, redirigir al login
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Si no se especificó ningún controlador pero el usuario está autenticado, ir al dashboard
if (empty($controller) && isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard');
    exit;
}

// Si no se especificó controlador y el usuario NO está autenticado, mostrar pantalla de inicio o ir al login
if (empty($controller) && !isset($_SESSION['user_id'])) {
    // Puedes optar por mostrar una pantalla de bienvenida o redirigir al login directamente
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Verificar si el controlador existe
$controller_file = "controllers/{$controller}_controller.php";

if (file_exists($controller_file)) {
    include_once $controller_file;
    
    // Crear el nombre de la clase del controlador
    $controller_class = ucfirst($controller) . "Controller";
    
    if (class_exists($controller_class)) {
        $controller_obj = new $controller_class();
        
        // Verificar si el método existe
        if (method_exists($controller_obj, $action)) {
            // Ejecutar el método del controlador
            $controller_obj->$action();
        } else {
            // Método no encontrado, cargar página de error
            include_once "views/404.php";
        }
    } else {
        // Clase no encontrada, cargar página de error
        include_once "views/404.php";
    }
} else {
    // Controlador no encontrado, cargar página de error
    include_once "views/404.php";
}
?>
</body>
</html>