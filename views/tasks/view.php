<?php
// Incluir el layout header
include_once 'views/layout_header.php';
?>

<div class="p-6">
    <!-- Breadcrumb and Page Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center">
                <a href="index.php?controller=tasks" class="text-mep-primary hover:text-mep-primary-dark mr-2">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo $task['title']; ?></h1>
            </div>
            <p class="text-sm text-gray-600 mt-1">Proyecto: <a href="index.php?controller=projects&action=view&id=<?php echo $task['project_id']; ?>" class="text-mep-primary hover:text-mep-primary-dark"><?php echo $task['project_name']; ?></a></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <div class="flex items-center">
                <span class="px-2 py-1 text-xs rounded-full bg-<?php 
                    echo $task['status'] === 'pendiente' ? 'yellow-100 text-yellow-800' : 
                        ($task['status'] === 'en_progreso' ? 'blue-100 text-blue-800' : 
                        'green-100 text-green-800'); 
                ?>">
                    <?php echo ucfirst($task['status']); ?>
                </span>
                
                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-<?php 
                    echo $task['priority'] === 'alta' ? 'red-100 text-red-800' : 
                        ($task['priority'] === 'media' ? 'yellow-100 text-yellow-800' : 
                        'green-100 text-green-800'); 
                ?>">
                    <?php echo ucfirst($task['priority']); ?>
                </span>
            </div>
            
            <a href="index.php?controller=tasks&action=edit&id=<?php echo $task['id']; ?>" class="px-4 py-2 bg-mep-primary text-white rounded-lg flex items-center justify-center">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Editar
            </a>
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg flex items-center justify-center" onclick="toggleDropdown()">
                <i data-lucide="more-horizontal" class="w-4 h-4"></i>
            </button>
            
            <!-- Dropdown menu -->
            <div id="dropdown" class="hidden absolute right-6 mt-10 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1 z-10">
                <?php if ($task['status'] !== 'completada'): ?>
                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $task['id']; ?>&status=completada" class="text-gray-700 hover:bg-gray-100 group flex items-center px-4 py-2 text-sm rounded-md">
                        <i data-lucide="check" class="w-4 h-4 mr-3 text-green-600"></i>
                        Marcar como completada
                    </a>
                <?php else: ?>
                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $task['id']; ?>&status=pendiente" class="text-gray-700 hover:bg-gray-100 group flex items-center px-4 py-2 text-sm rounded-md">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-3 text-yellow-600"></i>
                        Reabrir tarea
                    </a>
                <?php endif; ?>
                
                <?php if ($task['status'] === 'pendiente'): ?>
                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $task['id']; ?>&status=en_progreso" class="text-gray-700 hover:bg-gray-100 group flex items-center px-4 py-2 text-sm rounded-md">
                        <i data-lucide="play" class="w-4 h-4 mr-3 text-blue-600"></i>
                        Iniciar tarea
                    </a>
                <?php endif; ?>
                
                <a href="#" class="text-gray-700 hover:bg-gray-100 group flex items-center px-4 py-2 text-sm rounded-md">
                    <i data-lucide="file-text" class="w-4 h-4 mr-3 text-gray-600"></i>
                    Exportar a PDF
                </a>
                
                <hr class="my-1 border-gray-200">
                
                <a href="index.php?controller=tasks&action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')" class="text-gray-700 hover:bg-gray-100 group flex items-center px-4 py-2 text-sm rounded-md text-red-600">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-3"></i>
                    Eliminar tarea
                </a>
            </div>
        </div>
    </div>
    
    <!-- Task Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main task info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800">Información de la Tarea</h2>
                </div>
                <div class="p-5">
                    <div class="mb-5">
                        <h3 class="text-md font-medium text-gray-800 mb-2">Descripción</h3>
                        <p class="text-gray-600"><?php echo $task['description']; ?></p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <h3 class="text-md font-medium text-gray-800 mb-2">Detalles</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Asignado a:</span>
                                    <span class="text-gray-800"><?php echo $task['assigned_to_name']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Creado por:</span>
                                    <span class="text-gray-800"><?php echo $task['created_by_name']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fecha de inicio:</span>
                                    <span class="text-gray-800"><?php echo date('d/m/Y', strtotime($task['start_date'])); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fecha límite:</span>
                                    <span class="text-gray-800 <?php echo strtotime($task['due_date']) < time() && $task['status'] !== 'completada' ? 'text-red-600 font-medium' : ''; ?>"><?php echo date('d/m/Y', strtotime($task['due_date'])); ?></span>
                                </div>
                                <?php if ($task['status'] === 'completada' && !empty($task['completed_date'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fecha de finalización:</span>
                                    <span class="text-green-600"><?php echo date('d/m/Y', strtotime($task['completed_date'])); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Creado:</span>
                                    <span class="text-gray-800"><?php echo date('d/m/Y H:i', strtotime($task['created_at'])); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Actualizado:</span>
                                    <span class="text-gray-800"><?php echo date('d/m/Y H:i', strtotime($task['updated_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-md font-medium text-gray-800 mb-2">Progreso</h3>
                            <div class="mb-3">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Completado</span>
                                    <span class="text-sm font-medium text-gray-700"><?php echo $task['completion_percentage']; ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-mep-primary h-2.5 rounded-full" style="width: <?php echo $task['completion_percentage']; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <div>Inicio: <?php echo date('d/m/Y', strtotime($task['start_date'])); ?></div>
                                    <div>Fin: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>