<div class="p-6">
    <!-- Breadcrumb and Page Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proyectos</h1>
            <p class="text-sm text-gray-600 mt-1">Gestión y seguimiento de proyectos</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
            <a href="index.php?controller=projects&action=create" class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nuevo Proyecto
            </a>
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg flex items-center justify-center">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                Filtrar
            </button>
        </div>
    </div>
    
    <!-- Filters and search -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center mb-4">
            <div class="w-full md:w-64 relative">
                <input 
                    type="text" 
                    placeholder="Buscar proyectos..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"></i>
            </div>
            
            <div class="flex flex-wrap gap-2 mt-4 md:mt-0 md:ml-4">
                <select class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm appearance-none">
                    <option>Todos los estados</option>
                    <option>En progreso</option>
                    <option>Completados</option>
                    <option>Iniciados</option>
                    <option>En pausa</option>
                </select>
                
                <select class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm appearance-none">
                    <option>Todos los clientes</option>
                    <option>Técnicas Avanzadas S.L.</option>
                    <option>InnoSystems Ibérica</option>
                    <option>Ayuntamiento de Mijas</option>
                    <option>Cafeterías Costa del Sol</option>
                    <option>Construcciones Mediterráneo</option>
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
    
    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <?php foreach ($proyectos as $proyecto): ?>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden transform transition-all duration-200 hover:shadow-md hover:-translate-y-1">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $proyecto['name']; ?></h3>
                        <div class="dropdown relative">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-500 mt-1"><?php echo $proyecto['client']; ?></p>
                    
                    <div class="mt-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Progreso</span>
                            <span class="text-sm font-medium text-gray-700"><?php echo $proyecto['progress']; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="h-2 rounded-full <?php 
                                    echo $proyecto['progress'] < 30 ? 'bg-red-500' : 
                                        ($proyecto['progress'] < 70 ? 'bg-yellow-500' : 'bg-green-500'); 
                                ?>" 
                                style="width: <?php echo $proyecto['progress']; ?>%"
                            ></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full <?php 
                                echo $proyecto['status'] === 'En progreso' ? 'bg-blue-100 text-blue-800' : 
                                    ($proyecto['status'] === 'Completado' ? 'bg-green-100 text-green-800' : 
                                    'bg-yellow-100 text-yellow-800'); 
                            ?>">
                                <?php echo $proyecto['status']; ?>
                            </span>
                            
                            <span class="ml-2 px-2 py-1 text-xs rounded-full <?php 
                                echo $proyecto['priority'] === 'Alta' ? 'bg-red-100 text-red-800' : 
                                    ($proyecto['priority'] === 'Media' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-green-100 text-green-800'); 
                            ?>">
                                <?php echo $proyecto['priority']; ?>
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            <i data-lucide="calendar" class="w-4 h-4 inline-block"></i>
                            <?php echo $proyecto['deadline']; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex -space-x-2">
                            <?php foreach ($proyecto['team'] as $index => $member): ?>
                                <div class="w-8 h-8 rounded-full bg-<?php 
                                    echo $index === 0 ? 'blue' : ($index === 1 ? 'green' : 'purple'); 
                                ?>-500 flex items-center justify-center text-white text-xs font-medium border-2 border-white">
                                    <?php echo $member; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-sm font-medium text-gray-700">
                            <?php echo $proyecto['budget']; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="index.php?controller=projects&action=view&id=<?php echo $proyecto['id']; ?>" class="text-blue-600 text-sm font-medium hover:underline">
                            Ver detalles →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white rounded-lg shadow-sm px-6 py-3 flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Anterior
            </a>
            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Siguiente
            </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando <span class="font-medium">1</span> a <span class="font-medium"><?php echo count($proyectos); ?></span> de <span class="font-medium"><?php echo count($proyectos); ?></span> resultados
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Anterior</span>
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </a>
                    <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        1
                    </a>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        2
                    </a>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium">
                        3
                    </a>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        10
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Siguiente</span>
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>