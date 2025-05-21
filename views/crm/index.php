<!-- views/crm/index.php -->
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Clientes (CRM)</h1>
            <p class="text-sm text-gray-600 mt-1">Administra tus clientes y contactos</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="index.php?controller=crm&action=create" class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nuevo Cliente
            </a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <p><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        </div>
    <?php endif; ?>
    
    <!-- Filtros y búsqueda -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form action="index.php" method="GET" class="flex flex-col md:flex-row md:items-center">
            <input type="hidden" name="controller" value="crm">
            
            <div class="w-full md:w-64 relative">
                <input 
                    type="text" 
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Buscar clientes..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
            
            <div class="mt-4 md:mt-0 md:ml-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Buscar
                </button>
                
                <?php if (!empty($search)): ?>
                    <a href="index.php?controller=crm" class="ml-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg">
                        Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Lista de clientes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (empty($clientes)): ?>
            <div class="p-8 text-center text-gray-500">
                <?php if (!empty($search)): ?>
                    No se encontraron clientes que coincidan con la búsqueda "<?php echo htmlspecialchars($search); ?>".
                <?php else: ?>
                    No hay clientes registrados aún. ¡Agrega tu primer cliente!
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cliente['company_name']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($cliente['contact_name']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($cliente['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($cliente['phone']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php 
                                            $location = [];
                                            if (!empty($cliente['city'])) $location[] = $cliente['city'];
                                            if (!empty($cliente['country'])) $location[] = $cliente['country'];
                                            echo htmlspecialchars(implode(', ', $location));
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $statusClasses = [
                                            'activo' => 'bg-green-100 text-green-800',
                                            'inactivo' => 'bg-red-100 text-red-800',
                                            'potencial' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $statusClass = $statusClasses[$cliente['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                        <?php echo ucfirst(htmlspecialchars($cliente['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="index.php?controller=crm&action=view&id=<?php echo $cliente['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-2">
                                        <i data-lucide="eye" class="w-4 h-4 inline-block"></i> Ver
                                    </a>
                                    <a href="index.php?controller=crm&action=edit&id=<?php echo $cliente['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-2">
                                        <i data-lucide="edit" class="w-4 h-4 inline-block"></i> Editar
                                    </a>
                                    <a href="index.php?controller=crm&action=delete&id=<?php echo $cliente['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este cliente?')" class="text-red-600 hover:text-red-900">
                                        <i data-lucide="trash" class="w-4 h-4 inline-block"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>