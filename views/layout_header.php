<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP-Projects | <?php echo isset($title) ? $title : 'Sistema de Gestión'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>styles.css?v=<?= APP_VERSION ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header/Navbar con los nuevos colores corporativos -->
    <header class="bg-mep-primary text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-xl font-bold flex items-center">
                        <span class="mr-2">MEP-Projects</span>
                    </a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="index.php?controller=dashboard" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'dashboard' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">Dashboard</a>
                        <a href="index.php?controller=projects" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'projects' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">Proyectos</a>
                        <a href="index.php?controller=crm" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'crm' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">CRM</a>
                        <a href="index.php?controller=erp" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'erp' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">ERP</a>
                        <a href="index.php?controller=rrhh" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'rrhh' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">RRHH</a>
                        <a href="index.php?controller=chat" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'chat' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">Chat</a>
                        <a href="index.php?controller=tasks" class="hover:text-green-100 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'tasks' ? 'font-medium border-b-2 border-white pb-1' : ''; ?>">Tareas</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Buscador rápido -->
                    <div class="hidden md:block relative">
                        <input type="text" placeholder="Buscar..." class="bg-mep-primary-dark text-white placeholder-green-200 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-white w-40" />
                        <i data-lucide="search" class="absolute right-2 top-1.5 w-4 h-4 text-green-200"></i>
                    </div>
                    
                    <!-- Notificaciones -->
                    <div class="relative" id="notifications-dropdown">
                        <button class="focus:outline-none">
                            <i data-lucide="bell" class="w-5 h-5 cursor-pointer"></i>
                            <?php if (isset($unread_count) && $unread_count > 0): ?>
                                <span id="unread-badge" class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full w-4 h-4 flex items-center justify-center">
                                    <?php echo $unread_count; ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Dropdown de notificaciones -->
                        <div class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-20 hidden" id="notifications-menu">
                            <div class="py-2 px-4 bg-gray-100 border-b border-gray-200 font-medium text-gray-800 flex justify-between items-center">
                                <span>Notificaciones</span>
                                <?php if (isset($unread_count) && $unread_count > 0): ?>
                                    <span class="text-xs bg-red-500 text-white px-2 py-1 rounded-full"><?php echo $unread_count; ?> nuevas</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="max-h-64 overflow-y-auto">
                                <?php if (isset($notifications) && !empty($notifications)): ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <a href="<?php echo $notification['url']; ?>" class="block py-2 px-4 hover:bg-gray-50 border-b border-gray-100">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <?php if ($notification['type'] == 'message'): ?>
                                                        <i data-lucide="message-circle" class="w-5 h-5 text-blue-500"></i>
                                                    <?php elseif ($notification['type'] == 'task'): ?>
                                                        <i data-lucide="check-square" class="w-5 h-5 text-green-500"></i>
                                                    <?php else: ?>
                                                        <i data-lucide="bell" class="w-5 h-5 text-yellow-500"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($notification['title']); ?></p>
                                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($notification['content']); ?></p>
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        <?php 
                                                        $time = new DateTime($notification['time']);
                                                        $now = new DateTime();
                                                        $diff = $now->diff($time);
                                                        
                                                        if ($diff->d > 0) {
                                                            echo 'Hace ' . $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
                                                        } elseif ($diff->h > 0) {
                                                            echo 'Hace ' . $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
                                                        } elseif ($diff->i > 0) {
                                                            echo 'Hace ' . $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
                                                        } else {
                                                            echo 'Hace unos segundos';
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="py-4 px-4 text-center text-gray-500">
                                        No tienes notificaciones nuevas
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="py-2 px-4 bg-gray-50 text-center border-t border-gray-100">
                                <a href="#" class="text-sm text-blue-600 hover:underline">Ver todas las notificaciones</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menú usuario -->
                    <div class="relative" id="user-dropdown">
                        <button class="bg-mep-primary-dark rounded-md px-3 py-1 flex items-center space-x-2 cursor-pointer focus:outline-none">
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-mep-primary font-semibold">
                                <?php 
                                // Mostrar iniciales del usuario si están disponibles en la sesión
                                echo isset($_SESSION['user_name']) ? substr($_SESSION['user_name'], 0, 1) : 'U'; 
                                ?>
                            </div>
                            <span class="hidden md:inline">
                                <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'; ?>
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        
                        <!-- Dropdown del perfil -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-20 hidden" id="user-menu">
                            <div class="py-2">
                                <a href="index.php?controller=profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i data-lucide="user" class="w-4 h-4 inline-block mr-2"></i> Mi Perfil
                                </a>
                                <a href="index.php?controller=settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i data-lucide="settings" class="w-4 h-4 inline-block mr-2"></i> Configuración
                                </a>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <a href="index.php?controller=auth&action=users" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i data-lucide="users" class="w-4 h-4 inline-block mr-2"></i> Gestión de Usuarios
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="index.php?controller=auth&action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i data-lucide="log-out" class="w-4 h-4 inline-block mr-2"></i> Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Menú móvil (se muestra en pantallas pequeñas) -->
        <div class="md:hidden bg-mep-primary-dark">
            <div class="container mx-auto px-4 py-2">
                <div class="flex overflow-x-auto space-x-4 scrollbar-hide">
                    <a href="index.php?controller=dashboard" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'dashboard' ? 'font-medium' : ''; ?>">
                        <i data-lucide="home" class="w-4 h-4 inline-block"></i> Dashboard
                    </a>
                    <a href="index.php?controller=projects" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'projects' ? 'font-medium' : ''; ?>">
                        <i data-lucide="briefcase" class="w-4 h-4 inline-block"></i> Proyectos
                    </a>
                    <a href="index.php?controller=crm" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'crm' ? 'font-medium' : ''; ?>">
                        <i data-lucide="users" class="w-4 h-4 inline-block"></i> CRM
                    </a>
                    <a href="index.php?controller=erp" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'erp' ? 'font-medium' : ''; ?>">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 inline-block"></i> ERP
                    </a>
                    <a href="index.php?controller=rrhh" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'rrhh' ? 'font-medium' : ''; ?>">
                        <i data-lucide="user" class="w-4 h-4 inline-block"></i> RRHH
                    </a>
                    <a href="index.php?controller=chat" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'chat' ? 'font-medium' : ''; ?>">
                        <i data-lucide="message-circle" class="w-4 h-4 inline-block"></i> Chat
                    </a>
                    <a href="index.php?controller=tasks" class="text-sm text-white hover:text-green-100 whitespace-nowrap py-1 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'tasks' ? 'font-medium' : ''; ?>">
                        <i data-lucide="check-square" class="w-4 h-4 inline-block"></i> Tareas
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-1">