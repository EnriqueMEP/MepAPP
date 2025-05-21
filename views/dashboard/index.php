<?php
$title = "Dashboard";
ob_start();
?>

<div class="p-6">
    <!-- Welcome and Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">¡Bienvenido, Juan!</h1>
            <p class="text-gray-600"><?php echo date('l, d F, Y'); ?></p>
        </div>
        <div class="mt-4 md:mt-0 w-full md:w-auto">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Buscar..." 
                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 w-full md:w-64"
                />
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Proyectos Activos</p>
                    <h3 class="text-2xl font-bold mt-1"><?php echo $stats['proyectos_activos']; ?></h3>
                    <p class="text-green-600 text-sm flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +3 este mes
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i data-lucide="briefcase" class="text-blue-600 w-6 h-6"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tareas Pendientes</p>
                    <h3 class="text-2xl font-bold mt-1"><?php echo $stats['tareas_pendientes']; ?></h3>
                    <p class="text-red-600 text-sm flex items-center mt-2">
                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i> 4 con retraso
                    </p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i data-lucide="check-square" class="text-orange-600 w-6 h-6"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ingresos Mensuales</p>
                    <h3 class="text-2xl font-bold mt-1"><?php echo number_format($stats['ingresos_mensuales'], 0, ',', '.'); ?>€</h3>
                    <p class="text-green-600 text-sm flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +12,5% vs mes anterior
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="text-green-600 w-6 h-6"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Clientes Activos</p>
                    <h3 class="text-2xl font-bold mt-1"><?php echo $stats['clientes_activos']; ?></h3>
                    <p class="text-blue-600 text-sm flex items-center mt-2">
                        <i data-lucide="users" class="w-4 h-4 mr-1"></i> 8 nuevos este trimestre
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i data-lucide="users" class="text-purple-600 w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Widget -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-bold text-gray-800">Proyectos Recientes</h2>
        </div>
        <div class="p-5">
            <div class="space-y-4">
                <?php foreach ($proyectos_recientes as $proyecto): ?>
                    <div class="flex items-center border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex-1 pr-4">
                            <h3 class="font-medium text-gray-800"><?php echo $proyecto['name']; ?></h3>
                            <div class="flex items-center mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div 
                                        class="h-2 rounded-full <?php echo $proyecto['priority'] === 'Alta' ? 'bg-red-500' : ($proyecto['priority'] === 'Media' ? 'bg-yellow-500' : 'bg-green-500'); ?>" 
                                        style="width: <?php echo $proyecto['progress']; ?>%"
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo $proyecto['progress']; ?>%</span>
                            </div>
                        </div>
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full <?php 
                                echo $proyecto['status'] === 'En progreso' ? 'bg-blue-100 text-blue-800' : 
                                    ($proyecto['status'] === 'Completado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); 
                            ?>">
                                <?php echo $proyecto['status']; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="mt-4 text-blue-600 text-sm font-medium">Ver todos los proyectos →</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>