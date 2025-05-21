<?php
// Incluir el layout header
include_once 'views/layout_header.php';
?>

<div class="p-6">
    <!-- Encabezado y botones de acción -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tareas</h1>
            <p class="text-sm text-gray-600 mt-1">Gestión y seguimiento de tareas</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
            <a href="index.php?controller=tasks&action=create" class="px-4 py-2 bg-mep-primary text-white rounded-lg flex items-center justify-center">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nueva Tarea
            </a>
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg flex items-center justify-center">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                Filtrar
            </button>
        </div>
    </div>
    
    <!-- Filtros y búsqueda -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center mb-4">
            <div class="w-full md:w-64 relative">
                <input 
                    type="text" 
                    placeholder="Buscar tareas..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                />
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
            
            <div class="flex flex-wrap gap-2 mt-4 md:mt-0 md:ml-4">
                <select class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm appearance-none">
                    <option>Todos los estados</option>
                    <option>Pendientes</option>
                    <option>En progreso</option>
                    <option>Completadas</option>
                    <option>Canceladas</option>
                </select>
                
                <select class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm appearance-none">
                    <option>Todos los proyectos</option>
                    <?php foreach ($proyectos as $proyecto): ?>
                        <option value="<?php echo $proyecto['id']; ?>"><?php echo $proyecto['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm appearance-none">
                    <option>Todas las prioridades</option>
                    <option>Alta</option>
                    <option>Media</option>
                    <option>Baja</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Secciones de tareas -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Tareas pendientes -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Tareas pendientes <span class="text-mep-primary">(<?php echo count(array_filter($tareas, function($t) { return $t['status'] === 'pendiente'; })); ?>)</span></h2>
                <button class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="chevron-up" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarea</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha límite</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $tareasFiltered = array_filter($tareas, function($tarea) {
                            return $tarea['status'] === 'pendiente';
                        });
                        
                        foreach ($tareasFiltered as $tarea): 
                        ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="index.php?controller=tasks&action=view&id=<?php echo $tarea['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark font-medium"><?php echo $tarea['title']; ?></a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700"><?php echo $tarea['project_name']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                        echo $tarea['priority'] === 'alta' ? 'bg-red-100 text-red-800' : 
                                            ($tarea['priority'] === 'media' ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-green-100 text-green-800'); 
                                    ?>">
                                        <?php echo ucfirst($tarea['priority']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm <?php echo strtotime($tarea['due_date']) < time() ? 'text-red-600 font-medium' : 'text-gray-700'; ?>">
                                        <?php echo date('d/m/Y', strtotime($tarea['due_date'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $tarea['id']; ?>&status=en_progreso" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i data-lucide="play" class="w-4 h-4 inline-block"></i> Iniciar
                                    </a>
                                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $tarea['id']; ?>&status=completada" class="text-green-600 hover:text-green-900 mr-3">
                                        <i data-lucide="check" class="w-4 h-4 inline-block"></i> Completar
                                    </a>
                                    <a href="index.php?controller=tasks&action=edit&id=<?php echo $tarea['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark">
                                        <i data-lucide="edit" class="w-4 h-4 inline-block"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($tareasFiltered)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay tareas pendientes
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tareas en progreso -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Tareas en progreso <span class="text-blue-600">(<?php echo count(array_filter($tareas, function($t) { return $t['status'] === 'en_progreso'; })); ?>)</span></h2>
                <button class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="chevron-up" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarea</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha límite</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $tareasFiltered = array_filter($tareas, function($tarea) {
                            return $tarea['status'] === 'en_progreso';
                        });
                        
                        foreach ($tareasFiltered as $tarea): 
                        ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="index.php?controller=tasks&action=view&id=<?php echo $tarea['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark font-medium"><?php echo $tarea['title']; ?></a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700"><?php echo $tarea['project_name']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                        echo $tarea['priority'] === 'alta' ? 'bg-red-100 text-red-800' : 
                                            ($tarea['priority'] === 'media' ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-green-100 text-green-800'); 
                                    ?>">
                                        <?php echo ucfirst($tarea['priority']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm <?php echo strtotime($tarea['due_date']) < time() ? 'text-red-600 font-medium' : 'text-gray-700'; ?>">
                                        <?php echo date('d/m/Y', strtotime($tarea['due_date'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $tarea['id']; ?>&status=completada" class="text-green-600 hover:text-green-900 mr-3">
                                        <i data-lucide="check" class="w-4 h-4 inline-block"></i> Completar
                                    </a>
                                    <a href="index.php?controller=tasks&action=edit&id=<?php echo $tarea['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark">
                                        <i data-lucide="edit" class="w-4 h-4 inline-block"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($tareasFiltered)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay tareas en progreso
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tareas completadas -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Tareas completadas <span class="text-green-600">(<?php echo count(array_filter($tareas, function($t) { return $t['status'] === 'completada'; })); ?>)</span></h2>
                <button class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="chevron-down" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="overflow-x-auto hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarea</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completada</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $tareasFiltered = array_filter($tareas, function($tarea) {
                            return $tarea['status'] === 'completada';
                        });
                        
                        foreach ($tareasFiltered as $tarea): 
                        ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="index.php?controller=tasks&action=view&id=<?php echo $tarea['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark font-medium"><?php echo $tarea['title']; ?></a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700"><?php echo $tarea['project_name']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                        echo $tarea['priority'] === 'alta' ? 'bg-red-100 text-red-800' : 
                                            ($tarea['priority'] === 'media' ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-green-100 text-green-800'); 
                                    ?>">
                                        <?php echo ucfirst($tarea['priority']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700">
                                        <?php echo isset($tarea['completed_date']) ? date('d/m/Y', strtotime($tarea['completed_date'])) : '-'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="index.php?controller=tasks&action=change_status&id=<?php echo $tarea['id']; ?>&status=pendiente" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        <i data-lucide="refresh-cw" class="w-4 h-4 inline-block"></i> Reabrir
                                    </a>
                                    <a href="index.php?controller=tasks&action=view&id=<?php echo $tarea['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark">
                                        <i data-lucide="eye" class="w-4 h-4 inline-block"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($tareasFiltered)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay tareas completadas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funcionalidad para expandir/colapsar secciones
        const sectionButtons = document.querySelectorAll('[data-lucide="chevron-up"], [data-lucide="chevron-down"]');
        sectionButtons.forEach(button => {
            button.parentElement.addEventListener('click', function() {
                const contentSection = this.closest('div').nextElementSibling;
                contentSection.classList.toggle('hidden');
                
                const icon = this.querySelector('[data-lucide]');
                if (icon.getAttribute('data-lucide') === 'chevron-down') {
                    icon.setAttribute('data-lucide', 'chevron-up');
                } else {
                    icon.setAttribute('data-lucide', 'chevron-down');
                }
                
                // Volver a crear el icono para que se actualice
                lucide.createIcons({
                    icons: [icon],
                    replaceElements: true
                });
            });
        });
    });
</script>

<?php
// Incluir el footer del layout
include_once 'views/layout_footer.php';
?>