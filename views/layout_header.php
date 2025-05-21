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
    <!-- Header/Navbar -->
    <header class="bg-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-xl font-bold">MEP-Projects</a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="index.php?controller=dashboard" class="hover:text-blue-200 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'dashboard' ? 'font-medium' : ''; ?>">Dashboard</a>
                        <a href="index.php?controller=projects" class="hover:text-blue-200 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'projects' ? 'font-medium' : ''; ?>">Proyectos</a>
                        <a href="index.php?controller=crm" class="hover:text-blue-200 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'crm' ? 'font-medium' : ''; ?>">CRM</a>
                        <a href="index.php?controller=erp" class="hover:text-blue-200 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'erp' ? 'font-medium' : ''; ?>">ERP</a>
                        <a href="index.php?controller=rrhh" class="hover:text-blue-200 <?php echo isset($_GET['controller']) && $_GET['controller'] == 'rrhh' ? 'font-medium' : ''; ?>">RRHH</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full w-4 h-4 flex items-center justify-center">3</span>
                    </div>
                    <div class="bg-blue-700 rounded-md px-3 py-1 flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-blue-800 font-semibold">JP</div>
                        <span class="hidden md:inline">Juan Pérez</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-1">