<?php
// controllers/erp_controller.php
require_once __DIR__ . '/base_controller.php';

class ErpController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Lista de facturas
     */
    public function index() {
        // Control de acceso opcional
        $this->requirePermission('erp', 'read');

        // Traer facturas (tabla invoices + customers)
        $sql = "
            SELECT
              i.id,
              c.name   AS customer,
              i.amount,
              i.created_at
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            ORDER BY i.created_at DESC
        ";
        $stmt    = $this->db->query($sql);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderiza la vista views/erp/index.php
        $this->render('erp/index', [
            'title'    => 'ERP - Facturas',
            'invoices' => $invoices
        ]);
    }
}
