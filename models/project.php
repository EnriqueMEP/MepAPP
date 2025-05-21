<?php
// models/project.php
class Project {
    private $conn;
    private $table = 'projects';
    
    // Propiedades del proyecto
    public $id;
    public $name;
    public $description;
    public $client_id;
    public $start_date;
    public $end_date;
    public $status;
    public $priority;
    public $progress;
    public $budget;
    public $created_by;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Obtener todos los proyectos con posibles filtros
    public function read($status = '', $client = '', $priority = '', $search = '') {
        // Construir consulta base
        $query = "SELECT p.*, c.company_name as client, u.full_name as created_by_name
                 FROM " . $this->table . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN users u ON p.created_by = u.id";
        
        // Aplicar filtros si existen
        $conditions = [];
        $params = [];
        
        if(!empty($status)) {
            $conditions[] = "p.status = :status";
            $params[':status'] = $status;
        }
        
        if(!empty($client)) {
            $conditions[] = "p.client_id = :client_id";
            $params[':client_id'] = $client;
        }
        
        if(!empty($priority)) {
            $conditions[] = "p.priority = :priority";
            $params[':priority'] = $priority;
        }
        
        if(!empty($search)) {
            $conditions[] = "(p.name LIKE :search OR p.description LIKE :search OR c.company_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        // Añadir condiciones a la consulta
        if(!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Ordenar por fecha de actualización
        $query .= " ORDER BY p.updated_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Vincular parámetros
        foreach($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        
        // Obtener y transformar resultados
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        
        foreach($projects as $project) {
            // Obtener miembros del equipo
            $team_query = "SELECT u.id, u.full_name, pm.role, SUBSTRING(u.full_name, 1, 1) as initials
                          FROM project_members pm
                          JOIN users u ON pm.user_id = u.id
                          WHERE pm.project_id = :project_id";
            $team_stmt = $this->conn->prepare($team_query);
            $team_stmt->bindParam(':project_id', $project['id']);
            $team_stmt->execute();
            
            $team = $team_stmt->fetchAll(PDO::FETCH_ASSOC);
            $team_initials = array_column($team, 'initials');
            
            $project['team'] = $team;
            $project['team_initials'] = $team_initials;
            
            // Formatear fechas
            $project['start_date'] = date('d/m/Y', strtotime($project['start_date']));
            if($project['end_date']) {
                $project['end_date'] = date('d/m/Y', strtotime($project['end_date']));
            }
            
            // Formatear presupuesto
            $project['budget_formatted'] = number_format($project['budget'], 2, ',', '.') . '€';
            
            $result[] = $project;
        }
        
        return $result;
    }
    
    // Obtener un proyecto específico
    public function read_single() {
        $query = "SELECT p.*, c.company_name as client, u.full_name as created_by_name
                 FROM " . $this->table . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN users u ON p.created_by = u.id
                 WHERE p.id = :id
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$row) {
            return false;
        }
        
        // Obtener miembros del equipo
        $team_query = "SELECT u.id, u.full_name, pm.role, SUBSTRING(u.full_name, 1, 1) as initials
                      FROM project_members pm
                      JOIN users u ON pm.user_id = u.id
                      WHERE pm.project_id = :project_id";
        $team_stmt = $this->conn->prepare($team_query);
        $team_stmt->bindParam(':project_id', $row['id']);
        $team_stmt->execute();
        
        $team = $team_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $row['team'] = $team;
        
        // Formatear fechas
        $row['start_date'] = date('d/m/Y', strtotime($row['start_date']));
        if($row['end_date']) {
            $row['end_date'] = date('d/m/Y', strtotime($row['end_date']));
        }
        
        // Formatear presupuesto
        $row['budget_formatted'] = number_format($row['budget'], 2, ',', '.') . '€';
        
        return $row;
    }
    
    // Crear un nuevo proyecto
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                 (name, description, client_id, start_date, end_date, status, priority, progress, budget, created_by)
                 VALUES
                 (:name, :description, :client_id, :start_date, :end_date, :status, :priority, :progress, :budget, :created_by)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->client_id = htmlspecialchars(strip_tags($this->client_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));
        $this->progress = htmlspecialchars(strip_tags($this->progress));
        $this->budget = htmlspecialchars(strip_tags($this->budget));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));
        
        // Vincular parámetros
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':client_id', $this->client_id);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':progress', $this->progress);
        $stmt->bindParam(':budget', $this->budget);
        $stmt->bindParam(':created_by', $this->created_by);
        
        // Ejecutar consulta
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Actualizar un proyecto existente
    public function update() {
        $query = "UPDATE " . $this->table . "
                 SET name = :name,
                     description = :description,
                     client_id = :client_id,
                     start_date = :start_date,
                     end_date = :end_date,
                     status = :status,
                     priority = :priority,
                     progress = :progress,
                     budget = :budget
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->client_id = htmlspecialchars(strip_tags($this->client_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));
        $this->progress = htmlspecialchars(strip_tags($this->progress));
        $this->budget = htmlspecialchars(strip_tags($this->budget));
        
        // Vincular parámetros
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':client_id', $this->client_id);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':progress', $this->progress);
        $stmt->bindParam(':budget', $this->budget);
        
        // Ejecutar consulta
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Eliminar un proyecto
    public function delete() {
        // Primero eliminar las relaciones (miembros del equipo y tareas)
        $this->clearMembers($this->id);
        
        $delete_tasks = "DELETE FROM tasks WHERE project_id = :project_id";
        $task_stmt = $this->conn->prepare($delete_tasks);
        $task_stmt->bindParam(':project_id', $this->id);
        $task_stmt->execute();
        
        // Ahora eliminar el proyecto
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Añadir un miembro al equipo del proyecto
    public function addMember($project_id, $user_id, $role = '') {
        $query = "INSERT INTO project_members (project_id, user_id, role)
                 VALUES (:project_id, :user_id, :role)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }
    
    // Eliminar todos los miembros del equipo del proyecto
    public function clearMembers($project_id) {
        $query = "DELETE FROM project_members WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        
        return $stmt->execute();
    }
    
    // Obtener los miembros del equipo de un proyecto
    public function getTeamMembers($project_id) {
        $query = "SELECT pm.*, u.full_name, u.email
                 FROM project_members pm
                 JOIN users u ON pm.user_id = u.id
                 WHERE pm.project_id = :project_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>