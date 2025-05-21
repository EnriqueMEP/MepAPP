<?php
// controllers/rrhh_controller.php
require_once __DIR__ . '/base_controller.php';

class RrhhController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Lista de empleados (tabla employees)
     */
    public function index() {
        $this->requirePermission('rrhh', 'read');

        // Si no existe todavía la tabla employees, adapta a tu esquema real
        $sql = "
            SELECT id, full_name, department, active
            FROM employees
            ORDER BY full_name
        ";
        $stmt      = $this->db->query($sql);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('rrhh/index', [
            'title'     => 'RRHH - Empleados',
            'employees' => $employees
        ]);
    }
}
