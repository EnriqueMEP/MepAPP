<?php
// controllers/dashboard_controller.php
require_once __DIR__ . '/base_controller.php';

class DashboardController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // Verificar permisos
        $this->requirePermission('dashboard', 'read');

        // Obtener ID de usuario actual
        $userId = $_SESSION['user_id'];

        // 1. Estadísticas dinámicas
        // Proyectos activos
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM projects WHERE status = 'En progreso'");
        $stmt->execute();
        $stats['proyectos_activos'] = (int) $stmt->fetchColumn();

        // Tareas pendientes para el usuario
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status != 'Completada'"
        );
        $stmt->execute([$userId]);
        $stats['tareas_pendientes'] = (int) $stmt->fetchColumn();

        // Ingresos del mes actual
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM invoices
             WHERE MONTH(created_at) = MONTH(CURDATE())
               AND YEAR(created_at) = YEAR(CURDATE())"
        );
        $stmt->execute();
        $stats['ingresos_mensuales'] = (float) $stmt->fetchColumn();

        // Clientes activos
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM customers WHERE active = 1");
        $stmt->execute();
        $stats['clientes_activos'] = (int) $stmt->fetchColumn();

        // 2. Proyectos recientes (últimos 5)
        $stmt = $this->db->prepare(
            "SELECT name, progress, status
               FROM projects
              ORDER BY created_at DESC
              LIMIT 5"
        );
        $stmt->execute();
        $proyectos_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Tareas próximas (hasta 5)
        $stmt = $this->db->prepare(
            "SELECT name, priority, DATE_FORMAT(due_date, '%d/%m/%Y') AS date
               FROM tasks
              WHERE assigned_to = ? AND status != 'Completada'
              ORDER BY due_date ASC
              LIMIT 5"
        );
        $stmt->execute([$userId]);
        $tareas_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar la vista con datos dinámicos
        $this->render(
            'dashboard/index',
            [
                'title'               => 'Dashboard',
                'stats'               => $stats,
                'proyectos_recientes' => $proyectos_recientes,
                'tareas_pendientes'   => $tareas_pendientes
            ]
        );
    }
}
