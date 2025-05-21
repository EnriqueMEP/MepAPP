<?php
// controllers/dashboard_controller.php
require_once 'controllers/base_controller.php';

class DashboardController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Verificar permisos
        $this->requirePermission('dashboard', 'read');
        
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
        
        // Renderizar vista
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'proyectos_recientes' => $proyectos_recientes,
            'tareas_pendientes' => $tareas_pendientes
        ]);
    }
}
?>