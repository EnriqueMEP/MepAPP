<?php
class TasksController {
    private $db;
    private $user;
    
    public function __construct() {
        // Inicializar base de datos
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Inicializar modelo de usuario
        require_once 'models/user.php';
        $this->user = new User($this->db);
        
        // Verificar si el usuario está autenticado
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
    
    // Método principal - Lista de tareas
    public function index() {
        // Simulación de datos de tareas
        $tareas = [
            [
                'id' => 1,
                'title' => 'Revisar propuesta técnica',
                'description' => 'Verificar los detalles técnicos y ajustar presupuesto',
                'project_id' => 1,
                'project_name' => 'Diseño portal web MEP-2025',
                'assigned_to' => $_SESSION['user_id'],
                'status' => 'pendiente',
                'priority' => 'alta',
                'due_date' => '2025-05-22',
                'created_by' => 2,
                'created_by_name' => 'Ana López'
            ],
            [
                'id' => 2,
                'title' => 'Actualizar documentación API',
                'description' => 'Completar la documentación de la API REST para el portal',
                'project_id' => 1,
                'project_name' => 'Diseño portal web MEP-2025',
                'assigned_to' => $_SESSION['user_id'],
                'status' => 'en_progreso',
                'priority' => 'media',
                'due_date' => '2025-05-23',
                'created_by' => 1,
                'created_by_name' => 'Juan Pérez'
            ],
            [
                'id' => 3,
                'title' => 'Enviar informe mensual',
                'description' => 'Preparar y enviar el informe de actividades del mes',
                'project_id' => 3,
                'project_name' => 'Migración servidores cloud',
                'assigned_to' => $_SESSION['user_id'],
                'status' => 'pendiente',
                'priority' => 'alta',
                'due_date' => '2025-05-21',
                'created_by' => 3,
                'created_by_name' => 'Carlos Gómez'
            ],
            [
                'id' => 4,
                'title' => 'Preparar reunión equipo',
                'description' => 'Agenda y temas para la reunión semanal de coordinación',
                'project_id' => 4,
                'project_name' => 'Mantenimiento anual Sistemas',
                'assigned_to' => $_SESSION['user_id'],
                'status' => 'pendiente',
                'priority' => 'baja',
                'due_date' => '2025-05-22',
                'created_by' => 1,
                'created_by_name' => 'Juan Pérez'
            ],
            [
                'id' => 5,
                'title' => 'Revisar entregable cliente',
                'description' => 'Verificar la calidad del entregable antes de enviarlo',
                'project_id' => 1,
                'project_name' => 'Diseño portal web MEP-2025',
                'assigned_to' => $_SESSION['user_id'],
                'status' => 'completada',
                'priority' => 'alta',
                'due_date' => '2025-05-18',
                'completed_date' => '2025-05-18',
                'created_by' => 2,
                'created_by_name' => 'Ana López'
            ],
            [
                'id' => 6,
                'title' => 'Instalar servidor pruebas',
                'description' => 'Configurar el entorno de pruebas para el nuevo desarrollo',
                'project_id' => 2,
                'project_name' => 'Implementación ERP Fábrica Málaga',
                'assigned_to' => $_SESSION['user_id'],
                'status' => 'en_progreso',
                'priority' => 'media',
                'due_date' => '2025-05-25',
                'created_by' => 3,
                'created_by_name' => 'Carlos Gómez'
            ],
        ];
        
        // Simulación de proyectos para el filtro
        $proyectos = [
            [
                'id' => 1,
                'name' => 'Diseño portal web MEP-2025'
            ],
            [
                'id' => 2,
                'name' => 'Implementación ERP Fábrica Málaga'
            ],
            [
                'id' => 3,
                'name' => 'Migración servidores cloud'
            ],
            [
                'id' => 4,
                'name' => 'Mantenimiento anual Sistemas'
            ]
        ];
        
        // Título de la página
        $title = "Tareas";
        
        // Cargar la vista
        include_once 'views/tasks/index.php';
    }
    
    // Ver detalle de una tarea
    public function view() {
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Simulación de datos de tarea específica
        $task = [
            'id' => $task_id,
            'title' => 'Revisar propuesta técnica',
            'description' => 'Verificar los detalles técnicos y ajustar presupuesto según las nuevas especificaciones del cliente. Revisar con el equipo de desarrollo las estimaciones de tiempo para cada funcionalidad.',
            'project_id' => 1,
            'project_name' => 'Diseño portal web MEP-2025',
            'assigned_to' => $_SESSION['user_id'],
            'assigned_to_name' => 'Juan Pérez',
            'status' => 'pendiente',
            'priority' => 'alta',
            'start_date' => '2025-05-15',
            'due_date' => '2025-05-22',
            'created_by' => 2,
            'created_by_name' => 'Ana López',
            'created_at' => '2025-05-14 10:30:21',
            'updated_at' => '2025-05-20 15:22:45',
            'completed_date' => null,
            'completion_percentage' => 60,
            'comments' => [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'user_name' => 'Ana López',
                    'content' => 'He actualizado las especificaciones con los cambios que comentó el cliente ayer.',
                    'created_at' => '2025-05-18 09:15:30'
                ],
                [
                    'id' => 2,
                    'user_id' => $_SESSION['user_id'],
                    'user_name' => 'Juan Pérez',
                    'content' => 'Revisaré la nueva documentación y ajustaré el presupuesto hoy mismo.',
                    'created_at' => '2025-05-18 11:30:45'
                ],
                [
                    'id' => 3,
                    'user_id' => 3,
                    'user_name' => 'Carlos Gómez',
                    'content' => 'El equipo de desarrollo necesitará 3 días adicionales para implementar las nuevas funcionalidades.',
                    'created_at' => '2025-05-19 14:22:10'
                ]
            ],
            'subtasks' => [
                [
                    'id' => 1,
                    'title' => 'Revisar documento de requisitos',
                    'is_completed' => true
                ],
                [
                    'id' => 2,
                    'title' => 'Actualizar estimaciones de tiempo',
                    'is_completed' => true
                ],
                [
                    'id' => 3,
                    'title' => 'Ajustar presupuesto',
                    'is_completed' => false
                ],
                [
                    'id' => 4,
                    'title' => 'Preparar presentación para el cliente',
                    'is_completed' => false
                ]
            ],
            'attachments' => [
                [
                    'id' => 1,
                    'filename' => 'especificaciones_tecnicas_v2.pdf',
                    'size' => '1.2 MB',
                    'uploaded_by' => 2,
                    'uploaded_by_name' => 'Ana López',
                    'uploaded_at' => '2025-05-18 09:10:22'
                ],
                [
                    'id' => 2,
                    'filename' => 'presupuesto_inicial.xlsx',
                    'size' => '540 KB',
                    'uploaded_by' => $_SESSION['user_id'],
                    'uploaded_by_name' => 'Juan Pérez',
                    'uploaded_at' => '2025-05-16 14:45:30'
                ]
            ]
        ];
        
        // Obtener usuarios para asignación
        $users = [
            [
                'id' => 1,
                'full_name' => 'Juan Pérez',
                'email' => 'juan@mep-projects.com'
            ],
            [
                'id' => 2,
                'full_name' => 'Ana López',
                'email' => 'ana@mep-projects.com'
            ],
            [
                'id' => 3,
                'full_name' => 'Carlos Gómez',
                'email' => 'carlos@mep-projects.com'
            ],
            [
                'id' => 4,
                'full_name' => 'María Rodríguez',
                'email' => 'maria@mep-projects.com'
            ]
        ];
        
        // Título de la página
        $title = "Detalle de Tarea";
        
        // Cargar la vista
        include_once 'views/tasks/view.php';
    }
    
    // Crear una nueva tarea
    public function create() {
        // Obtener proyectos para el formulario
        $proyectos = [
            [
                'id' => 1,
                'name' => 'Diseño portal web MEP-2025'
            ],
            [
                'id' => 2,
                'name' => 'Implementación ERP Fábrica Málaga'
            ],
            [
                'id' => 3,
                'name' => 'Migración servidores cloud'
            ],
            [
                'id' => 4,
                'name' => 'Mantenimiento anual Sistemas'
            ]
        ];
        
        // Obtener usuarios para asignación
        $users = [
            [
                'id' => 1,
                'full_name' => 'Juan Pérez',
                'email' => 'juan@mep-projects.com'
            ],
            [
                'id' => 2,
                'full_name' => 'Ana López',
                'email' => 'ana@mep-projects.com'
            ],
            [
                'id' => 3,
                'full_name' => 'Carlos Gómez',
                'email' => 'carlos@mep-projects.com'
            ],
            [
                'id' => 4,
                'full_name' => 'María Rodríguez',
                'email' => 'maria@mep-projects.com'
            ]
        ];
        
        $success = "";
        $error = "";
        
        // Procesar el formulario si se envió
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aquí iría la lógica para guardar la tarea
            // Por ahora simulamos éxito
            $success = "Tarea creada correctamente";
            
            // Redirigir a la lista de tareas después de crear
            $_SESSION['success'] = $success;
            header('Location: index.php?controller=tasks');
            exit;
        }
        
        // Título de la página
        $title = "Nueva Tarea";
        
        // Cargar la vista
        include_once 'views/tasks/create.php';
    }
    
    // Editar una tarea existente
    public function edit() {
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Simulación de datos de tarea específica
        $task = [
            'id' => $task_id,
            'title' => 'Revisar propuesta técnica',
            'description' => 'Verificar los detalles técnicos y ajustar presupuesto según las nuevas especificaciones del cliente.',
            'project_id' => 1,
            'assigned_to' => $_SESSION['user_id'],
            'status' => 'pendiente',
            'priority' => 'alta',
            'start_date' => '2025-05-15',
            'due_date' => '2025-05-22'
        ];
        
        // Obtener proyectos para el formulario
        $proyectos = [
            [
                'id' => 1,
                'name' => 'Diseño portal web MEP-2025'
            ],
            [
                'id' => 2,
                'name' => 'Implementación ERP Fábrica Málaga'
            ],
            [
                'id' => 3,
                'name' => 'Migración servidores cloud'
            ],
            [
                'id' => 4,
                'name' => 'Mantenimiento anual Sistemas'
            ]
        ];
        
        // Obtener usuarios para asignación
        $users = [
            [
                'id' => 1,
                'full_name' => 'Juan Pérez',
                'email' => 'juan@mep-projects.com'
            ],
            [
                'id' => 2,
                'full_name' => 'Ana López',
                'email' => 'ana@mep-projects.com'
            ],
            [
                'id' => 3,
                'full_name' => 'Carlos Gómez',
                'email' => 'carlos@mep-projects.com'
            ],
            [
                'id' => 4,
                'full_name' => 'María Rodríguez',
                'email' => 'maria@mep-projects.com'
            ]
        ];
        
        $success = "";
        $error = "";
        
        // Procesar el formulario si se envió
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aquí iría la lógica para actualizar la tarea
            // Por ahora simulamos éxito
            $success = "Tarea actualizada correctamente";
            
            // Redirigir a la vista de la tarea después de editar
            $_SESSION['success'] = $success;
            header('Location: index.php?controller=tasks&action=view&id=' . $task_id);
            exit;
        }
        
        // Título de la página
        $title = "Editar Tarea";
        
        // Cargar la vista
        include_once 'views/tasks/edit.php';
    }
    
    // Cambiar el estado de una tarea
    public function change_status() {
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        if ($task_id > 0 && !empty($status)) {
            // Aquí iría la lógica para actualizar el estado de la tarea
            // Por ahora simulamos éxito
            $_SESSION['success'] = "Estado de la tarea actualizado correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el estado de la tarea";
        }
        
        // Redirigir a la vista anterior
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?controller=tasks';
        header('Location: ' . $referer);
        exit;
    }
    
    // Eliminar una tarea
    public function delete() {
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($task_id > 0) {
            // Aquí iría la lógica para eliminar la tarea
            // Por ahora simulamos éxito
            $_SESSION['success'] = "Tarea eliminada correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar la tarea";
        }
        
        // Redirigir a la lista de tareas
        header('Location: index.php?controller=tasks');
        exit;
    }
}
?>