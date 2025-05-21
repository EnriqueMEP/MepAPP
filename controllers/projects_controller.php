<?php
// controllers/projects_controller.php
require_once 'controllers/base_controller.php';

class ProjectsController extends BaseController {
    private $project;
    private $task;
    
    public function __construct() {
        parent::__construct();
        
        // Inicializar modelos
        require_once 'models/project.php';
        require_once 'models/task.php';
        $this->project = new Project($this->db);
        $this->task = new Task($this->db);
    }
    
    // Listar proyectos
    public function index() {
        // Verificar permisos
        $this->requirePermission('projects', 'read');
        
        // Obtener todos los proyectos o aplicar filtros
        $filter_status = isset($_GET['status']) ? $_GET['status'] : '';
        $filter_client = isset($_GET['client']) ? $_GET['client'] : '';
        $filter_priority = isset($_GET['priority']) ? $_GET['priority'] : '';
        $search_term = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Obtener proyectos según filtros
        $proyectos = $this->project->read($filter_status, $filter_client, $filter_priority, $search_term);
        
        // Renderizar vista
        $this->render('projects/index', [
            'title' => 'Proyectos',
            'proyectos' => $proyectos,
            'filter_status' => $filter_status,
            'filter_client' => $filter_client,
            'filter_priority' => $filter_priority,
            'search_term' => $search_term
        ]);
    }
    
    // Ver un proyecto específico
    public function view() {
        // Verificar permisos
        $this->requirePermission('projects', 'read');
        
        // Obtener ID del proyecto
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            header('Location: index.php?controller=projects');
            exit;
        }
        
        // Obtener datos del proyecto
        $this->project->id = $id;
        $proyecto = $this->project->read_single();
        
        if(!$proyecto) {
            $_SESSION['error'] = 'Proyecto no encontrado';
            header('Location: index.php?controller=projects');
            exit;
        }
        
        // Obtener tareas del proyecto
        $tareas = $this->task->readByProject($id);
        $proyecto['tasks'] = $tareas;
        
        // Renderizar vista
        $this->render('projects/view', [
            'title' => $proyecto['name'],
            'proyecto' => $proyecto
        ]);
    }
    
    // Crear nuevo proyecto
    public function create() {
        // Verificar permisos
        $this->requirePermission('projects', 'write');
        
        $error = '';
        $success = '';
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            if(empty($_POST['name']) || empty($_POST['client_id'])) {
                $error = 'Por favor complete todos los campos requeridos';
            } else {
                // Configurar modelo
                $this->project->name = $_POST['name'];
                $this->project->description = $_POST['description'] ?? '';
                $this->project->client_id = intval($_POST['client_id']);
                $this->project->start_date = $_POST['start_date'] ?? date('Y-m-d');
                $this->project->end_date = $_POST['end_date'] ?? null;
                $this->project->status = $_POST['status'] ?? 'iniciado';
                $this->project->priority = $_POST['priority'] ?? 'media';
                $this->project->progress = $_POST['progress'] ?? 0;
                $this->project->budget = $_POST['budget'] ?? 0;
                $this->project->created_by = $_SESSION['user_id'];
                
                // Intentar crear
                if($this->project->create()) {
                    // Añadir miembros al proyecto si se seleccionaron
                    if(isset($_POST['team_members']) && is_array($_POST['team_members'])) {
                        foreach($_POST['team_members'] as $member_id) {
                            $this->project->addMember($this->project->id, $member_id, $_POST['member_role'][$member_id] ?? '');
                        }
                    }
                    
                    $success = 'Proyecto creado correctamente';
                    
                    // Redirigir a la vista del proyecto
                    header('Location: index.php?controller=projects&action=view&id=' . $this->project->id);
                    exit;
                } else {
                    $error = 'Error al crear el proyecto';
                }
            }
        }
        
        // Obtener clientes para el dropdown
        require_once 'models/client.php';
        $client_model = new Client($this->db);
        $clients = $client_model->read();
        
        // Obtener usuarios para seleccionar equipo
        require_once 'models/user.php';
        $user_model = new User($this->db);
        $users = $user_model->read()->fetchAll(PDO::FETCH_ASSOC);
        
        // Renderizar vista
        $this->render('projects/create', [
            'title' => 'Nuevo Proyecto',
            'error' => $error,
            'success' => $success,
            'clients' => $clients,
            'users' => $users
        ]);
    }
    
    // Editar proyecto
    public function edit() {
        // Verificar permisos
        $this->requirePermission('projects', 'write');
        
        // Obtener ID del proyecto
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            header('Location: index.php?controller=projects');
            exit;
        }
        
        // Obtener datos del proyecto
        $this->project->id = $id;
        $proyecto = $this->project->read_single();
        
        if(!$proyecto) {
            $_SESSION['error'] = 'Proyecto no encontrado';
            header('Location: index.php?controller=projects');
            exit;
        }
        
        $error = '';
        $success = '';
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            if(empty($_POST['name']) || empty($_POST['client_id'])) {
                $error = 'Por favor complete todos los campos requeridos';
            } else {
                // Configurar modelo
                $this->project->id = $id;
                $this->project->name = $_POST['name'];
                $this->project->description = $_POST['description'] ?? '';
                $this->project->client_id = intval($_POST['client_id']);
                $this->project->start_date = $_POST['start_date'] ?? date('Y-m-d');
                $this->project->end_date = $_POST['end_date'] ?? null;
                $this->project->status = $_POST['status'] ?? 'iniciado';
                $this->project->priority = $_POST['priority'] ?? 'media';
                $this->project->progress = $_POST['progress'] ?? 0;
                $this->project->budget = $_POST['budget'] ?? 0;
                
                // Intentar actualizar
                if($this->project->update()) {
                    // Actualizar miembros del equipo
                    $this->project->clearMembers($id);
                    
                    if(isset($_POST['team_members']) && is_array($_POST['team_members'])) {
                        foreach($_POST['team_members'] as $member_id) {
                            $this->project->addMember($id, $member_id, $_POST['member_role'][$member_id] ?? '');
                        }
                    }
                    
                    $success = 'Proyecto actualizado correctamente';
                    
                    // Actualizar datos del proyecto en la vista
                    $proyecto = $this->project->read_single();
                } else {
                    $error = 'Error al actualizar el proyecto';
                }
            }
        }
        
        // Obtener clientes para el dropdown
        require_once 'models/client.php';
        $client_model = new Client($this->db);
        $clients = $client_model->read();
        
        // Obtener usuarios para seleccionar equipo
        require_once 'models/user.php';
        $user_model = new User($this->db);
        $users = $user_model->read()->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener miembros actuales del equipo
        $team_members = $this->project->getTeamMembers($id);
        
        // Renderizar vista
        $this->render('projects/edit', [
            'title' => 'Editar Proyecto: ' . $proyecto['name'],
            'error' => $error,
            'success' => $success,
            'proyecto' => $proyecto,
            'clients' => $clients,
            'users' => $users,
            'team_members' => $team_members
        ]);
    }
    
    // Eliminar proyecto
    public function delete() {
        // Verificar permisos
        $this->requirePermission('projects', 'delete');
        
        // Obtener ID del proyecto
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            header('Location: index.php?controller=projects');
            exit;
        }
        
        // Configurar modelo
        $this->project->id = $id;
        
        // Intentar eliminar
        if($this->project->delete()) {
            $_SESSION['success'] = 'Proyecto eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el proyecto';
        }
        
        // Redirigir a la lista de proyectos
        header('Location: index.php?controller=projects');
        exit;
    }
    
    // API: Obtener tareas de un proyecto (para AJAX)
    public function tasks() {
        // Verificar permisos
        $this->requirePermission('projects', 'read');
        
        // Verificar si es una petición AJAX
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            header('Location: index.php?controller=dashboard');
            exit;
        }
        
        // Obtener ID del proyecto
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id <= 0) {
            echo json_encode(['error' => 'ID de proyecto inválido']);
            exit;
        }
        
        // Obtener tareas del proyecto
        $tareas = $this->task->readByProject($id);
        
        // Devolver como JSON
        header('Content-Type: application/json');
        echo json_encode($tareas);
        exit;
    }
}
?>