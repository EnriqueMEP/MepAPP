<?php
class ProjectsController {
    // ... otros métodos y propiedades
    
    public function index() {
        // Simulación de datos de proyectos
        $proyectos = [
            [
                'id' => 1,
                'name' => 'Diseño portal web MEP-2025',
                'client' => 'Técnicas Avanzadas S.L.',
                'progress' => 75,
                'status' => 'En progreso',
                'priority' => 'Alta',
                'deadline' => '30/06/2025',
                'team' => ['AM', 'LP', 'CG'],
                'budget' => '15.000€'
            ],
            // ... otros proyectos
        ];
        
        // Cargar la vista
        include_once 'views/projects/index.php';
    }
}
?>