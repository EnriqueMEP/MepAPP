<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP-Projects | <?php echo isset($title) ? $title : 'Sistema de Gestión'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
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
                    <div class="relative">
                        <i data-lucide="bell" class="w-5 h-5 cursor-pointer"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full w-4 h-4 flex items-center justify-center">3</span>
                    </div>
                    <!-- Menú usuario -->
                    <div class="bg-mep-primary-dark rounded-md px-3 py-1 flex items-center space-x-2 cursor-pointer">
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