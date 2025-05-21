<?php
// controllers/tasks_controller.php

require_once __DIR__ . '/base_controller.php';
require_once __DIR__ . '/../models/task.php';

class TasksController extends BaseController {
    private $taskModel;

    public function __construct() {
        parent::__construct();
        $this->taskModel = new Task($this->db);
    }

    public function index() {
        // Permiso
        $this->requirePermission('tasks','read');
        $me = (int) $_SESSION['user_id'];

        // Sacar las tareas reales de la BBDD
        $pendientes  = $this->taskModel->readByStatus($me, 'pendiente');
        $enProgreso  = $this->taskModel->readByStatus($me, 'en_progreso');
        $completadas = $this->taskModel->readByStatus($me, 'completada');

        // Renderizamos pasando esos arrays
        $this->render('tasks/index', [
            'title'        => 'Tareas',
            'pendientes'   => $pendientes,
            'enProgreso'   => $enProgreso,
            'completadas'  => $completadas
        ]);
    }

    // Ver detalle de una tarea
       public function view() {
        $this->requirePermission('tasks','read');
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: index.php?controller=tasks');
            exit;
        }

        $task = $this->taskModel->readSingle($id);
        if (!$task) {
            header('Location: index.php?controller=tasks');
            exit;
        }

        $this->render('tasks/view', [
            'title' => 'Detalle de Tarea',
            'task'  => $task
        ]);
    }

    // Crear nueva tarea
    public function create() {
        $this->requirePermission('tasks','create');

        $proyectos = [ /* ... */ ];
        $users     = [ /* ... */ ];
        $success   = '';
        $error     = '';

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            // Aquí guardas realmente tu tarea en BD...
            $success = 'Tarea creada correctamente';
            $_SESSION['success'] = $success;
            header('Location: index.php?controller=tasks');
            exit;
        }

        $this->render('tasks/create', [
            'title'     => 'Nueva Tarea',
            'proyectos' => $proyectos,
            'users'     => $users,
            'success'   => $success,
            'error'     => $error
        ]);
    }

    // Editar tarea existente
    public function edit() {
        $this->requirePermission('tasks','update');
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $task = [ 'id'=>$task_id, 'title'=>'Revisar propuesta técnica', /* ... */ ];
        $proyectos = [ /* ... */ ];
        $users     = [ /* ... */ ];
        $success   = '';
        $error     = '';

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            // Aquí actualizas tu tarea en BD...
            $success = 'Tarea actualizada correctamente';
            $_SESSION['success'] = $success;
            header("Location: index.php?controller=tasks&action=view&id={$task_id}");
            exit;
        }

        $this->render('tasks/edit', [
            'title'     => 'Editar Tarea',
            'task'      => $task,
            'proyectos' => $proyectos,
            'users'     => $users,
            'success'   => $success,
            'error'     => $error
        ]);
    }

    // Cambiar el estado de la tarea
    public function change_status() {
        $this->requirePermission('tasks','update');
        $task_id = intval($_GET['id'] ?? 0);
        $status  = $_GET['status'] ?? '';

        if ($task_id && $status) {
            // Actualiza estado en BD...
            $_SESSION['success'] = 'Estado actualizado';
        } else {
            $_SESSION['error'] = 'Error al actualizar estado';
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?controller=tasks'));
        exit;
    }

    // Eliminar una tarea
    public function delete() {
        $this->requirePermission('tasks','delete');
        $task_id = intval($_GET['id'] ?? 0);

        if ($task_id) {
            // Borra de BD...
            $_SESSION['success'] = 'Tarea eliminada';
        } else {
            $_SESSION['error'] = 'Error al eliminar tarea';
        }
        header('Location: index.php?controller=tasks');
        exit;
    }
}
