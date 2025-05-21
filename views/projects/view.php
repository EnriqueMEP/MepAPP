<?php
// Incluir el layout sin cerrar el main
include_once 'views/layout_header.php';
?>

<div class="p-6">
    <!-- Breadcrumb and Page Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center">
                <a href="index.php?controller=projects" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo $proyecto['name']; ?></h1>
            </div>
            <p class="text-sm text-gray-600 mt-1">Cliente: <?php echo $proyecto['client']; ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Editar
            </button>
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg flex items-center justify-center">
                <i data-lucide="more-horizontal" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
    
    <!-- Project Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main project info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800">Información del Proyecto</h2>
                </div>
                <div class="p-5">
                    <div class="mb-5">
                        <h3 class="text-md font-medium text-gray-800 mb-2">Descripción</h3>
                        <p class="text-gray-600"><?php echo $proyecto['description']; ?></p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <h3 class="text-md font-medium text-gray-800 mb-2">Detalles</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Estado:</span>
                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                        echo $proyecto['status'] === 'En progreso' ? 'bg-blue-100 text-blue-800' : 
                                            ($proyecto['status'] === 'Completado' ? 'bg-green-100 text-green-800' : 
                                            'bg-yellow-100 text-yellow-800'); 
                                    ?>"><?php echo $proyecto['status']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Prioridad:</span>
                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                        echo $proyecto['priority'] === 'Alta' ? 'bg-red-100 text-red-800' : 
                                            ($proyecto['priority'] === 'Media' ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-green-100 text-green-800'); 
                                    ?>"><?php echo $proyecto['priority']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fecha de inicio:</span>
                                    <span class="text-gray-800"><?php echo $proyecto['start_date']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fecha límite:</span>
                                    <span class="text-gray-800"><?php echo $proyecto['deadline']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Presupuesto:</span>
                                    <span class="text-gray-800 font-medium"><?php echo $proyecto['budget']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-md font-medium text-gray-800 mb-2">Progreso</h3>
                            <div class="mb-3">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Completado</span>
                                    <span class="text-sm font-medium text-gray-700"><?php echo $proyecto['progress']; ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $proyecto['progress']; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <div>Inicio: <?php echo $proyecto['start_date']; ?></div>
                                    <div>Fin: <?php echo $proyecto['deadline']; ?></div>
                                </div>
                            </div>
                            
                            <h3 class="text-md font-medium text-gray-800 mb-2 mt-4">Equipo</h3>
                            <div class="space-y-3">
                                <?php foreach ($proyecto['team'] as $member): ?>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-medium">
                                            <?php echo $member['initials']; ?>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-800"><?php echo $member['name']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $member['role']; ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar - tasks -->
        <div>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Tareas</h2>
                    <button class="text-blue-600 hover:text-blue-800">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <?php foreach ($proyecto['tasks'] as $index => $task): ?>
                            <div class="flex items-center">
                                <div class="h-5 w-5 <?php echo $task['status'] === 'Completada' ? 'bg-blue-500 text-white flex items-center justify-center' : 'border-2 border-gray-300'; ?> rounded mr-3">
                                    <?php if ($task['status'] === 'Completada'): ?>
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm <?php echo $task['status'] === 'Completada' ? 'text-gray-500 line-through' : 'text-gray-800'; ?>"><?php echo $task['name']; ?></p>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs text-gray-500 mr-2"><?php echo $task['due_date']; ?></span>
                                        <span class="text-xs bg-gray-100 text-gray-700 rounded px-1"><?php echo $task['assignee']; ?></span>
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <span class="px-2 py-1 text-xs rounded-full <?php 
                                        echo $task['status'] === 'Completada' ? 'bg-green-100 text-green-800' : 
                                            ($task['status'] === 'En progreso' ? 'bg-blue-100 text-blue-800' : 
                                            'bg-yellow-100 text-yellow-800'); 
                                    ?>"><?php echo $task['status']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer del layout
include_once 'views/layout_footer.php';
?>