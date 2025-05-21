<?php
// controllers/crm_controller.php
require_once 'controllers/base_controller.php';

class CrmController extends BaseController {
    private $client;
    
    public function __construct() {
        parent::__construct();
        
        // Inicializar modelo de cliente
        require_once 'models/client.php';
        $this->client = new Client($this->db);
    }
    
    // Listar clientes
    public function index() {
        // Verificar permisos
        $this->requirePermission('crm', 'read');
        
        // Obtener parámetros de búsqueda
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Obtener clientes
        if(!empty($search)) {
            $clientes = $this->client->search($search);
        } else {
            $clientes = $this->client->read();
        }
        
        // Renderizar vista
        $this->render('crm/index', [
            'title' => 'Gestión de Clientes (CRM)',
            'clientes' => $clientes,
            'search' => $search
        ]);
    }
    
    // Ver un cliente específico
    public function view() {
        // Verificar permisos
        $this->requirePermission('crm', 'read');
        
        // Obtener ID del cliente
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            header('Location: index.php?controller=crm');
            exit;
        }
        
        // Obtener datos del cliente
        $this->client->id = $id;
        $cliente = $this->client->read_single();
        
        if(!$cliente) {
            $_SESSION['error'] = 'Cliente no encontrado';
            header('Location: index.php?controller=crm');
            exit;
        }
        
        // Obtener proyectos del cliente
        $proyectos = $this->client->getProjects();
        
        // Renderizar vista
        $this->render('crm/view', [
            'title' => $cliente['company_name'],
            'cliente' => $cliente,
            'proyectos' => $proyectos
        ]);
    }
    
    // Crear nuevo cliente
    public function create() {
        // Verificar permisos
        $this->requirePermission('crm', 'write');
        
        $error = '';
        $success = '';
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            if(empty($_POST['company_name']) || empty($_POST['contact_name']) || empty($_POST['email'])) {
                $error = 'Por favor complete todos los campos requeridos';
            } else {
                // Configurar modelo
                $this->client->user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
                $this->client->company_name = $_POST['company_name'];
                $this->client->contact_name = $_POST['contact_name'];
                $this->client->email = $_POST['email'];
                $this->client->phone = $_POST['phone'] ?? '';
                $this->client->address = $_POST['address'] ?? '';
                $this->client->city = $_POST['city'] ?? '';
                $this->client->country = $_POST['country'] ?? '';
                $this->client->postal_code = $_POST['postal_code'] ?? '';
                $this->client->website = $_POST['website'] ?? '';
                $this->client->notes = $_POST['notes'] ?? '';
                $this->client->status = $_POST['status'] ?? 'activo';
                
                // Intentar crear
                if($this->client->create()) {
                    $success = 'Cliente creado correctamente';
                    
                    // Redirigir a la vista del cliente
                    header('Location: index.php?controller=crm&action=view&id=' . $this->client->id);
                    exit;
                } else {
                    $error = 'Error al crear el cliente';
                }
            }
        }
        
        // Obtener usuarios para el dropdown
        require_once 'models/user.php';
        $user_model = new User($this->db);
        $users = $user_model->read()->fetchAll(PDO::FETCH_ASSOC);
        
        // Renderizar vista
        $this->render('crm/create', [
            'title' => 'Nuevo Cliente',
            'error' => $error,
            'success' => $success,
            'users' => $users
        ]);
    }
    
    // Editar cliente
    public function edit() {
        // Verificar permisos
        $this->requirePermission('crm', 'write');
        
        // Obtener ID del cliente
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            header('Location: index.php?controller=crm');
            exit;
        }
        
        // Obtener datos del cliente
        $this->client->id = $id;
        $cliente = $this->client->read_single();
        
        if(!$cliente) {
            $_SESSION['error'] = 'Cliente no encontrado';
            header('Location: index.php?controller=crm');
            exit;
        }
        
        $error = '';
        $success = '';
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            if(empty($_POST['company_name']) || empty($_POST['contact_name']) || empty($_POST['email'])) {
                $error = 'Por favor complete todos los campos requeridos';
            } else {
                // Configurar modelo
                $this->client->id = $id;
                $this->client->user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
                $this->client->company_name = $_POST['company_name'];
                $this->client->contact_name = $_POST['contact_name'];
                $this->client->email = $_POST['email'];
                $this->client->phone = $_POST['phone'] ?? '';
                $this->client->address = $_POST['address'] ?? '';
                $this->client->city = $_POST['city'] ?? '';
                $this->client->country = $_POST['country'] ?? '';
                $this->client->postal_code = $_POST['postal_code'] ?? '';
                $this->client->website = $_POST['website'] ?? '';
                $this->client->notes = $_POST['notes'] ?? '';
                $this->client->status = $_POST['status'] ?? 'activo';
                
                // Intentar actualizar
                if($this->client->update()) {
                    $success = 'Cliente actualizado correctamente';
                    
                    // Actualizar datos del cliente
                    $cliente = $this->client->read_single();
                } else {
                    $error = 'Error al actualizar el cliente';
                }
            }
        }
        
        // Obtener usuarios para el dropdown
        require_once 'models/user.php';
        $user_model = new User($this->db);
        $users = $user_model->read()->fetchAll(PDO::FETCH_ASSOC);
        
        // Renderizar vista
        $this->render('crm/edit', [
            'title' => 'Editar Cliente: ' . $cliente['company_name'],
            'error' => $error,
            'success' => $success,
            'cliente' => $cliente,
            'users' => $users
        ]);
    }
    
    // Eliminar cliente
    public function delete() {
        // Verificar permisos
        $this->requirePermission('crm', 'delete');
        
        // Obtener ID del cliente
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            header('Location: index.php?controller=crm');
            exit;
        }
        
        // Configurar modelo
        $this->client->id = $id;
        
        // Intentar eliminar
        if($this->client->delete()) {
            $_SESSION['success'] = 'Cliente eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el cliente. Puede tener proyectos asociados.';
        }
        
        // Redirigir a la lista de clientes
        header('Location: index.php?controller=crm');
        exit;
    }
}
?>