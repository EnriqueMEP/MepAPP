

<div class="p-6">
    <!-- Welcome and Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                ¡Bienvenido, <?php echo isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : 'Usuario'; ?>!
            </h1>
            <p class="text-gray-600"><?php echo date('l, d F Y'); ?></p>
        </div>
        <div class="mt-4 md:mt-0 w-full md:w-auto">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Buscar en todo el sistema..." 
                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 w-full md:w-64 focus:border-mep-primary focus:ring-1 focus:ring-mep-primary focus:outline-none"
                />
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Proyectos Activos</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?php echo $stats['proyectos_activos']; ?></h3>
                    <p class="text-mep-primary text-sm flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +3 este mes
                    </p>
                </div>
                <div class="bg-mep-primary bg-opacity-10 p-3 rounded-lg">
                    <i data-lucide="briefcase" class="text-mep-primary w-6 h-6"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tareas Pendientes</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?php echo $stats['tareas_pendientes']; ?></h3>
                    <p class="text-red-600 text-sm flex items-center mt-2">
                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i> 4 con retraso
                    </p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i data-lucide="check-square" class="text-orange-600 w-6 h-6"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ingresos Mensuales</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?php echo number_format($stats['ingresos_mensuales'], 0, ',', '.'); ?>€</h3>
                    <p class="text-mep-primary text-sm flex items-center mt-2">
                        <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +12,5% vs mes anterior
                    </p>
                </div>
                <div class="bg-mep-primary bg-opacity-10 p-3 rounded-lg">
                    <i data-lucide="euro" class="text-mep-primary w-6 h-6"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Clientes Activos</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?php echo $stats['clientes_activos']; ?></h3>
                    <p class="text-mep-primary text-sm flex items-center mt-2">
                        <i data-lucide="users" class="w-4 h-4 mr-1"></i> 8 nuevos este trimestre
                    </p>
                </div>
                <div class="bg-mep-primary bg-opacity-10 p-3 rounded-lg">
                    <i data-lucide="users" class="text-mep-primary w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Projects -->
        <div class="lg:col-span-2">
            <!-- Projects Widget -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Proyectos Recientes</h2>
                    <a href="index.php?controller=projects" class="text-mep-primary hover:text-mep-primary-dark text-sm font-medium">Ver todos</a>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <?php foreach ($proyectos_recientes as $index => $proyecto): ?>
                            <div class="flex items-center border-b border-gray-100 pb-4 <?php echo $index === count($proyectos_recientes) - 1 ? 'border-0 pb-0' : ''; ?>">
                                <div class="flex-1 pr-4">
                                    <h3 class="font-medium text-gray-800"><?php echo $proyecto['name']; ?></h3>
                                    <div class="flex items-center mt-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                            <div 
                                                class="h-2 rounded-full <?php 
                                                    if ($proyecto['progress'] < 30) {
                                                        echo 'bg-red-500';
                                                    } elseif ($proyecto['progress'] < 70) {
                                                        echo 'bg-yellow-500';
                                                    } else {
                                                        echo 'bg-mep-primary';
                                                    }
                                                ?>" 
                                                style="width: <?php echo $proyecto['progress']; ?>%"
                                            ></div>
                                        </div>
                                        <span class="text-xs text-gray-500"><?php echo $proyecto['progress']; ?>%</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                        if ($proyecto['status'] === 'En progreso') {
                                            echo 'bg-blue-100 text-blue-800';
                                        } elseif ($proyecto['status'] === 'Completado') {
                                            echo 'bg-green-100 text-green-800';
                                        } else {
                                            echo 'bg-yellow-100 text-yellow-800';
                                        }
                                    ?>">
                                        <?php echo $proyecto['status']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="index.php?controller=projects" class="mt-4 inline-block text-mep-primary hover:underline text-sm font-medium">Ver todos los proyectos →</a>
                </div>
            </div>
            
            <!-- Activity Graph Widget -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800">Actividad del Proyecto</h2>
                </div>
                <div class="p-5">
                    <div class="h-64 flex items-center justify-center">
                        <!-- Aquí irá un gráfico real con Chart.js o similar -->
                        <div class="text-center">
                            <div class="w-full h-40 bg-gray-100 rounded-lg flex items-end justify-around px-4 py-2">
                                <?php 
                                // Simulación de datos para el gráfico
                                $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
                                $valores = [65, 80, 55, 70, 90, 82];
                                
                                foreach ($valores as $index => $valor): 
                                    $altura = ($valor / 100) * 100; // Calcular altura proporcional
                                ?>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-mep-primary rounded-t w-6" style="height: <?php echo $altura; ?>%"></div>
                                        <div class="text-xs mt-1 text-gray-600"><?php echo $meses[$index]; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p class="mt-4 text-sm text-gray-600">Actividad mensual de proyectos (n° de tareas completadas)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Tasks and Calendar -->
        <div>
            <!-- Tasks Widget -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Tareas Pendientes</h2>
                    <a href="index.php?controller=tasks" class="text-mep-primary hover:text-mep-primary-dark text-sm font-medium">Ver todas</a>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <?php foreach ($tareas_pendientes as $tarea): ?>
                            <div class="flex items-center border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="h-5 w-5 border-2 border-gray-300 rounded mr-3 flex-shrink-0"></div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800"><?php echo $tarea['name']; ?></p>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500 mr-2"><?php echo $tarea['date']; ?></span>
                                        <span class="text-xs px-1.5 py-0.5 rounded <?php 
                                            if ($tarea['priority'] === 'Alta') {
                                                echo 'bg-red-100 text-red-800';
                                            } elseif ($tarea['priority'] === 'Media') {
                                                echo 'bg-yellow-100 text-yellow-800';
                                            } else {
                                                echo 'bg-green-100 text-green-800';
                                            }
                                        ?>">
                                            <?php echo $tarea['priority']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="index.php?controller=tasks&action=create" class="mt-4 inline-flex items-center text-mep-primary hover:underline text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Añadir nueva tarea
                    </a>
                </div>
            </div>
            
            <!-- Calendar Widget -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800">Calendario</h2>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-medium text-gray-700">Mayo 2025</h3>
                            <div class="flex space-x-2">
                                <button class="p-1 rounded hover:bg-gray-100">
                                    <i data-lucide="chevron-left" class="w-4 h-4 text-gray-500"></i>
                                </button>
                                <button class="p-1 rounded hover:bg-gray-100">
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-500"></i>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center">
                            <div class="text-xs font-medium text-gray-500">Lu</div>
                            <div class="text-xs font-medium text-gray-500">Ma</div>
                            <div class="text-xs font-medium text-gray-500">Mi</div>
                            <div class="text-xs font-medium text-gray-500">Ju</div>
                            <div class="text-xs font-medium text-gray-500">Vi</div>
                            <div class="text-xs font-medium text-gray-500">Sa</div>
                            <div class="text-xs font-medium text-gray-500">Do</div>
                            
                            <!-- Días del mes -->
                            <div class="text-xs py-1 text-gray-400">29</div>
                            <div class="text-xs py-1 text-gray-400">30</div>
                            <div class="text-xs py-1">1</div>
                            <div class="text-xs py-1">2</div>
                            <div class="text-xs py-1">3</div>
                            <div class="text-xs py-1">4</div>
                            <div class="text-xs py-1">5</div>
                            <div class="text-xs py-1">6</div>
                            <div class="text-xs py-1">7</div>
                            <div class="text-xs py-1">8</div>
                            <div class="text-xs py-1">9</div>
                            <div class="text-xs py-1">10</div>
                            <div class="text-xs py-1">11</div>
                            <div class="text-xs py-1">12</div>
                            <div class="text-xs py-1">13</div>
                            <div class="text-xs py-1">14</div>
                            <div class="text-xs py-1">15</div>
                            <div class="text-xs py-1">16</div>
                            <div class="text-xs py-1">17</div>
                            <div class="text-xs py-1">18</div>
                            <div class="text-xs py-1">19</div>
                            <div class="text-xs py-1">20</div>
                            <div class="text-xs py-1 bg-mep-primary text-white rounded-full">21</div>
                            <div class="text-xs py-1 bg-blue-100 text-blue-800 rounded">22</div>
                            <div class="text-xs py-1">23</div>
                            <div class="text-xs py-1">24</div>
                            <div class="text-xs py-1">25</div>
                            <div class="text-xs py-1">26</div>
                            <div class="text-xs py-1">27</div>
                            <div class="text-xs py-1">28</div>
                            <div class="text-xs py-1">29</div>
                            <div class="text-xs py-1">30</div>
                            <div class="text-xs py-1">31</div>
                            <div class="text-xs py-1 text-gray-400">1</div>
                            <div class="text-xs py-1 text-gray-400">2</div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mt-4">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-xs text-gray-600">Reunión con equipo</span>
                            <span class="text-xs text-gray-500 ml-auto">15:00</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-mep-primary rounded-full mr-2"></div>
                            <span class="text-xs text-gray-600">Entrega proyecto MEP-2025</span>
                            <span class="text-xs text-gray-500 ml-auto">17:30</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800">Accesos Rápidos</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="index.php?controller=projects&action=create" class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i data-lucide="plus-square" class="w-6 h-6 text-mep-primary mb-2"></i>
                            <span class="text-xs text-gray-700">Nuevo Proyecto</span>
                        </a>
                        <a href="index.php?controller=crm&action=create" class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i data-lucide="user-plus" class="w-6 h-6 text-mep-primary mb-2"></i>
                            <span class="text-xs text-gray-700">Nuevo Cliente</span>
                        </a>
                        <a href="index.php?controller=erp&action=invoice_create" class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i data-lucide="file-text" class="w-6 h-6 text-mep-primary mb-2"></i>
                            <span class="text-xs text-gray-700">Nueva Factura</span>
                        </a>
                        <a href="index.php?controller=reports" class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i data-lucide="bar-chart-2" class="w-6 h-6 text-mep-primary mb-2"></i>
                            <span class="text-xs text-gray-700">Informes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

