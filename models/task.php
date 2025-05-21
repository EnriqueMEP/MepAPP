<?php
// models/task.php
class Task {
    private $conn;
    private $table = 'tasks';
    
    // Propiedades de la tarea
    public $id;
    public $title;
    public $description;
    public $project_id;
    public $assigned_to;
    public $status;
    public $priority;
    public $start_date;
    public $due_date;
    public $completion_date;
    public $created_by;
    public $created_at;
    public $updated_at;
    
    public function __construct(PDO $db) {
        $this->conn = $db;
    }
    
    /**
     * Devuelve tareas de un usuario por estado
     */
    public function readByStatus(int $userId, string $status): array {
        $sql = "
          SELECT
            t.*,
            p.name    AS project_name,
            u1.full_name AS assigned_to_name,
            u2.full_name AS created_by_name
          FROM {$this->table} t
          LEFT JOIN projects p ON t.project_id   = p.id
          LEFT JOIN users    u1 ON t.assigned_to  = u1.id
          LEFT JOIN users    u2 ON t.created_by   = u2.id
          WHERE t.assigned_to = :userId
            AND t.status      = :status
          ORDER BY t.due_date ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lee una tarea por su ID
     */
    public function readSingle(int $id): ?array {
        $sql = "
          SELECT
            t.*,
            p.name    AS project_name,
            u1.full_name AS assigned_to_name,
            u2.full_name AS created_by_name
          FROM {$this->table} t
          LEFT JOIN projects p ON t.project_id   = p.id
          LEFT JOIN users    u1 ON t.assigned_to  = u1.id
          LEFT JOIN users    u2 ON t.created_by   = u2.id
          WHERE t.id = :id
          LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    
    // Obtener todas las tareas
    public function read() {
        $query = "SELECT t.*, p.name as project_name, u.full_name as assigned_to_name, c.full_name as created_by_name
                 FROM " . $this->table . " t
                 LEFT JOIN projects p ON t.project_id = p.id
                 LEFT JOIN users u ON t.assigned_to = u.id
                 LEFT JOIN users c ON t.created_by = c.id
                 ORDER BY t.due_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener tareas por proyecto
    public function readByProject($project_id) {
        $query = "SELECT t.*, u.full_name as assigned_to_name
                 FROM " . $this->table . " t
                 LEFT JOIN users u ON t.assigned_to = u.id
                 WHERE t.project_id = :project_id
                 ORDER BY t.due_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        
        foreach($tasks as $task) {
            // Formatear fechas
            if($task['start_date']) {
                $task['start_date_formatted'] = date('d/m/Y', strtotime($task['start_date']));
            }
            if($task['due_date']) {
                $task['due_date_formatted'] = date('d/m/Y', strtotime($task['due_date']));
            }
            if($task['completion_date']) {
                $task['completion_date_formatted'] = date('d/m/Y', strtotime($task['completion_date']));
            }
            
            $result[] = $task;
        }
        
        return $result;
    }
    
    // Obtener tareas asignadas a un usuario
    public function readByUser($user_id) {
        $query = "SELECT t.*, p.name as project_name
                 FROM " . $this->table . " t
                 LEFT JOIN projects p ON t.project_id = p.id
                 WHERE t.assigned_to = :user_id
                 ORDER BY t.due_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener una tarea específica
    public function read_single() {
        $query = "SELECT t.*, p.name as project_name, u.full_name as assigned_to_name, c.full_name as created_by_name
                 FROM " . $this->table . " t
                 LEFT JOIN projects p ON t.project_id = p.id
                 LEFT JOIN users u ON t.assigned_to = u.id
                 LEFT JOIN users c ON t.created_by = c.id
                 WHERE t.id = :id
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->project_id = $row['project_id'];
            $this->assigned_to = $row['assigned_to'];
            $this->status = $row['status'];
            $this->priority = $row['priority'];
            $this->start_date = $row['start_date'];
            $this->due_date = $row['due_date'];
            $this->completion_date = $row['completion_date'];
        }
        
        return $row;
    }
    
    // Crear una nueva tarea
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                 (title, description, project_id, assigned_to, status, priority, start_date, due_date, created_by)
                 VALUES
                 (:title, :description, :project_id, :assigned_to, :status, :priority, :start_date, :due_date, :created_by)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->project_id = htmlspecialchars(strip_tags($this->project_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));
        
        // Vincular parámetros
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':assigned_to', $this->assigned_to);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':due_date', $this->due_date);
        $stmt->bindParam(':created_by', $this->created_by);
        
        // Ejecutar consulta
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Actualizar una tarea existente
    public function update() {
        $query = "UPDATE " . $this->table . "
                 SET title = :title,
                     description = :description,
                     project_id = :project_id,
                     assigned_to = :assigned_to,
                     status = :status,
                     priority = :priority,
                     start_date = :start_date,
                     due_date = :due_date,
                     completion_date = :completion_date
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->project_id = htmlspecialchars(strip_tags($this->project_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));
        
        // Vincular parámetros
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':assigned_to', $this->assigned_to);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':due_date', $this->due_date);
        $stmt->bindParam(':completion_date', $this->completion_date);
        
        // Ejecutar consulta
        if($stmt->execute()) {
            // Si la tarea se completó, actualizar progreso del proyecto
            if($this->status === 'completada') {
                $this->updateProjectProgress($this->project_id);
            }
            
            return true;
        }
        
        return false;
    }
    
    // Eliminar una tarea
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            // Actualizar progreso del proyecto
            $this->updateProjectProgress($this->project_id);
            return true;
        }
        
        return false;
    }
    
    // Actualizar el progreso del proyecto basado en tareas completadas
    private function updateProjectProgress($project_id) {
        // Contar tareas totales
        $total_query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE project_id = :project_id";
        $total_stmt = $this->conn->prepare($total_query);
        $total_stmt->bindParam(':project_id', $project_id);
        $total_stmt->execute();
        $total = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if($total == 0) {
            return;
        }
        
        // Contar tareas completadas
        $completed_query = "SELECT COUNT(*) as completed FROM " . $this->table . " 
                            WHERE project_id = :project_id AND status = 'completada'";
        $completed_stmt = $this->conn->prepare($completed_query);
        $completed_stmt->bindParam(':project_id', $project_id);
        $completed_stmt->execute();
        $completed = $completed_stmt->fetch(PDO::FETCH_ASSOC)['completed'];
        
        // Calcular progreso
        $progress = round(($completed / $total) * 100);
        
        // Actualizar proyecto
        $update_query = "UPDATE projects SET progress = :progress WHERE id = :project_id";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bindParam(':progress', $progress);
        $update_stmt->bindParam(':project_id', $project_id);
        $update_stmt->execute();
    }
}
?>