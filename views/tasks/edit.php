<?php
// Incluir el layout header
include_once 'views/layout_header.php';
?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center">
                <a href="index.php?controller=tasks&action=view&id=<?php echo $task['id']; ?>" class="text-mep-primary hover:text-mep-primary-dark mr-2">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Editar Tarea</h1>
            </div>
            <p class="text-sm text-gray-600 mt-1">Modificar los detalles de la tarea</p>
        </div>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="index.php?controller=tasks&action=edit&id=<?php echo $task['id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Título</label>
                    <input type="text" id="title" name="title" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                        placeholder="Título de la tarea" value="<?php echo htmlspecialchars($task['title']); ?>">
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Descripción</label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                        placeholder="Describe detalladamente en qué consiste la tarea"><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
                
                <div>
                    <label for="project_id" class="block text-gray-700 font-medium mb-2">Proyecto</label>
                    <select id="project_id" name="project_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent">
                        <option value="">Seleccionar proyecto</option>
                        <?php foreach ($proyectos as $proyecto): ?>
                            <option value="<?php echo $proyecto['id']; ?>" <?php echo $proyecto['id'] == $task['project_id'] ? 'selected' : ''; ?>>
                                <?php echo $proyecto['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="assigned_to" class="block text-gray-700 font-medium mb-2">Asignar a</label>
                    <select id="assigned_to" name="assigned_to" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent">
                        <option value="">Seleccionar usuario</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo $user['id'] == $task['assigned_to'] ? 'selected' : ''; ?>>
                                <?php echo $user['full_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-gray-700 font-medium mb-2">Estado</label>
                    <select id="status" name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent">
                        <option value="pendiente" <?php echo $task['status'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_progreso" <?php echo $task['status'] == 'en_progreso' ? 'selected' : ''; ?>>En progreso</option>
                        <option value="completada" <?php echo $task['status'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                        <option value="cancelada" <?php echo $task['status'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                
                <div>
                    <label for="priority" class="block text-gray-700 font-medium mb-2">Prioridad</label>
                    <select id="priority" name="priority" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent">
                        <option value="baja" <?php echo $task['priority'] == 'baja' ? 'selected' : ''; ?>>Baja</option>
                        <option value="media" <?php echo $task['priority'] == 'media' ? 'selected' : ''; ?>>Media</option>
                        <option value="alta" <?php echo $task['priority'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
                    </select>
                </div>
                
                <div>
                    <label for="start_date" class="block text-gray-700 font-medium mb-2">Fecha de inicio</label>
                    <input type="date" id="start_date" name="start_date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                        value="<?php echo date('Y-m-d', strtotime($task['start_date'])); ?>">
                </div>
                
                <div>
                    <label for="due_date" class="block text-gray-700 font-medium mb-2">Fecha límite</label>
                    <input type="date" id="due_date" name="due_date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                        value="<?php echo date('Y-m-d', strtotime($task['due_date'])); ?>">
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-md font-medium text-gray-800 mb-2">Progreso</h3>
                    <div class="mb-2">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Porcentaje de finalización</span>
                            <span id="completion-percentage-text" class="text-sm font-medium text-gray-700">60%</span>
                        </div>
                        <input 
                            type="range" 
                            id="completion_percentage" 
                            name="completion_percentage" 
                            min="0" 
                            max="100" 
                            step="5" 
                            value="60"
                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-md font-medium text-gray-800 mb-2">Subtareas</h3>
                    <div id="subtasks-container" class="space-y-3 mb-2">
                        <!-- Aquí normalmente se cargarían las subtareas reales de la base de datos -->
                        <div class="subtask-item flex items-center">
                            <div class="mr-2">
                                <input type="checkbox" checked class="h-5 w-5 text-mep-primary focus:ring-mep-primary border-gray-300 rounded">
                            </div>
                            <input type="text" name="subtasks[]" placeholder="Subtarea"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                                value="Revisar documento de requisitos">
                            <input type="hidden" name="subtask_completed[]" value="1">
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700" onclick="removeSubtask(this)">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="subtask-item flex items-center">
                            <div class="mr-2">
                                <input type="checkbox" checked class="h-5 w-5 text-mep-primary focus:ring-mep-primary border-gray-300 rounded">
                            </div>
                            <input type="text" name="subtasks[]" placeholder="Subtarea"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                                value="Actualizar estimaciones de tiempo">
                            <input type="hidden" name="subtask_completed[]" value="1">
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700" onclick="removeSubtask(this)">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="subtask-item flex items-center">
                            <div class="mr-2">
                                <input type="checkbox" class="h-5 w-5 text-mep-primary focus:ring-mep-primary border-gray-300 rounded">
                            </div>
                            <input type="text" name="subtasks[]" placeholder="Subtarea"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent"
                                value="Ajustar presupuesto">
                            <input type="hidden" name="subtask_completed[]" value="0">
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700" onclick="removeSubtask(this)">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add-subtask" class="text-mep-primary hover:text-mep-primary-dark flex items-center text-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Añadir subtarea
                    </button>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-200 pt-6 flex justify-between">
                <a href="index.php?controller=tasks&action=view&id=<?php echo $task['id']; ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-mep-primary text-white rounded-lg hover:bg-mep-primary-dark transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funcionalidad para el slider de porcentaje
        const percentageSlider = document.getElementById('completion_percentage');
        const percentageText = document.getElementById('completion-percentage-text');
        
        percentageSlider.addEventListener('input', function() {
            percentageText.textContent = this.value + '%';
        });
        
        // Funcionalidad para las subtareas
        const checkboxes = document.querySelectorAll('#subtasks-container input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const hiddenInput = this.closest('.subtask-item').querySelector('input[type="hidden"]');
                hiddenInput.value = this.checked ? '1' : '0';
            });
        });
        
        // Funcionalidad para añadir subtareas
        const addSubtaskBtn = document.getElementById('add-subtask');
        const subtasksContainer = document.getElementById('subtasks-container');
        
        addSubtaskBtn.addEventListener('click', function() {
            const newSubtask = document.createElement('div');
            newSubtask.className = 'subtask-item flex items-center';
            newSubtask.innerHTML = `
                <div class="mr-2">
                    <input type="checkbox" class="h-5 w-5 text-mep-primary focus:ring-mep-primary border-gray-300 rounded">
                </div>
                <input type="text" name="subtasks[]" placeholder="Subtarea"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mep-primary focus:border-transparent">
                <input type="hidden" name="subtask_completed[]" value="0">
                <button type="button" class="ml-2 text-red-500 hover:text-red-700" onclick="removeSubtask(this)">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            `;
            
            subtasksContainer.appendChild(newSubtask);
            
            // Actualizar los iconos
            lucide.createIcons({
                icons: document.querySelectorAll('.subtask-item i'),
                nameAttr: 'data-lucide'
            });
            
            // Añadir event listener al nuevo checkbox
            const newCheckbox = newSubtask.querySelector('input[type="checkbox"]');
            newCheckbox.addEventListener('change', function() {
                const hiddenInput = this.closest('.subtask-item').querySelector('input[type="hidden"]');
                hiddenInput.value = this.checked ? '1' : '0';
            });
        });
    });
    
    function removeSubtask(button) {
        const subtaskItem = button.closest('.subtask-item');
        subtaskItem.remove();
    }
</script>

<?php
// Incluir el footer del layout
include_once 'views/layout_footer.php';
?>