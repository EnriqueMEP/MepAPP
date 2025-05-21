<?php
class DashboardController {
    private $database;
    
    public function __construct() {
        // Inicializar conexión a la base de datos
        require_once 'config/database.php';
        $db = new Database();
        $this->database = $db->getConnection();
    }
    
    public function index() {
        // Datos para el dashboard (simulados por ahora)
        $stats = [
            'proyectos_activos' => 24,
            'tareas_pendientes' => 18,
            'ingresos_mensuales' => 32840,
            'clientes_activos' => 56
        ];
        
        // Proyectos recientes (simulados)
        $proyectos_recientes = [
            [
                'name' => 'Diseño portal web MEP-2025',
                'progress' => 75,
                'status' => 'En progreso',
                'priority' => 'Alta'
            ],
            [
                'name' => 'Implementación ERP Fábrica Málaga',
                'progress' => 45,
                'status' => 'En progreso',
                'priority' => 'Media'
            ],
            [
                'name' => 'Migración servidores cloud',
                'progress' => 90,
                'status' => 'En progreso',
                'priority' => 'Alta'
            ],
            [
                'name' => 'Mantenimiento anual Sistemas',
                'progress' => 10,
                'status' => 'Iniciado',
                'priority' => 'Baja'
            ]
        ];
        
        // Tareas pendientes (simuladas)
        $tareas_pendientes = [
            [
                'name' => 'Revisar propuesta técnica',
                'priority' => 'Alta',
                'date' => 'Hoy'
            ],
            [
                'name' => 'Actualizar documentación API',
                'priority' => 'Media',
                'date' => 'Mañana'
            ],
            [
                'name' => 'Enviar informe mensual',
                'priority' => 'Alta',
                'date' => 'Hoy'
            ],
            [
                'name' => 'Preparar reunión equipo',
                'priority' => 'Baja',
                'date' => '22 May'
            ]
        ];
        
        // Establecer el título de la página
        $title = "Dashboard";
        
        // Cargar la vista con layout
        require_once 'views/layout.php';
    }
}