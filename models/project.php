<?php
// models/project.php

class Project {
    private $conn;
    private $table = 'projects';
    
    // Propiedades del proyecto (coinciden con columnas de la tabla)
    public $id;
    public $name;
    public $description;
    public $customer_id;
    public $start_date;
    public $end_date;
    public $status;
    public $priority;
    public $progress;
    public $budget;
    public $created_by;
    public $created_at;
    public $updated_at;
    
    public function __construct(PDO $db) {
        $this->conn = $db;
    }
    
    /**
     * Devuelve todos los proyectos sin filtros
     */
    public function readAll() {
        return $this->read();
    }
    
    /**
     * Devuelve proyectos aplicando opcionales filtros:
     *   $status   = 'En progreso', 'Completado', etc.
     *   $customer = ID del cliente
     *   $priority = 'Alta'|'Media'|'Baja'
     *   $search   = texto a buscar en nombre/descr/cliente
     */
    public function read($status = '', $customer = '', $priority = '', $search = '') {
        $sql = "
            SELECT
                p.*,
                cust.name      AS customer,
                u.full_name    AS created_by_name
            FROM {$this->table} p
            LEFT JOIN customers cust ON p.customer_id = cust.id
            LEFT JOIN users u         ON p.created_by  = u.id
        ";
        
        $conditions = [];
        $params     = [];
        
        if ($status !== '') {
            $conditions[]     = "p.status = :status";
            $params[':status'] = $status;
        }
        if ($customer !== '') {
            $conditions[]          = "p.customer_id = :customer_id";
            $params[':customer_id'] = $customer;
        }
        if ($priority !== '') {
            $conditions[]        = "p.priority = :priority";
            $params[':priority'] = $priority;
        }
        if ($search !== '') {
            $conditions[]            = "(p.name LIKE :search OR p.description LIKE :search OR cust.name LIKE :search)";
            $params[':search']       = "%{$search}%";
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY p.updated_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->formatProjects($projects);
    }
    
    /**
     * Devuelve un único proyecto cargado en $this->id
     */
    public function readSingle() {
        $sql = "
            SELECT
                p.*,
                cust.name      AS customer,
                u.full_name    AS created_by_name
            FROM {$this->table} p
            LEFT JOIN customers cust ON p.customer_id = cust.id
            LEFT JOIN users u         ON p.created_by  = u.id
            WHERE p.id = :id
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $formatted = $this->formatProjects([$row]);
        return $formatted[0];
    }
    
    /**
     * Crea un nuevo proyecto con todas las propiedades ya seteadas
     */
    public function create() {
        $sql = "
            INSERT INTO {$this->table}
              (name, description, customer_id, start_date, end_date,
               status, priority, progress, budget, created_by)
            VALUES
              (:name, :description, :customer_id, :start_date, :end_date,
               :status, :priority, :progress, :budget, :created_by)
        ";
        $stmt = $this->conn->prepare($sql);
        
        // Sanitizar / bind de parámetros
        $stmt->bindParam(':name',         $this->name);
        $stmt->bindParam(':description',  $this->description);
        $stmt->bindParam(':customer_id',  $this->customer_id);
        $stmt->bindParam(':start_date',   $this->start_date);
        $stmt->bindParam(':end_date',     $this->end_date);
        $stmt->bindParam(':status',       $this->status);
        $stmt->bindParam(':priority',     $this->priority);
        $stmt->bindParam(':progress',     $this->progress);
        $stmt->bindParam(':budget',       $this->budget);
        $stmt->bindParam(':created_by',   $this->created_by);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Actualiza un proyecto existente
     */
    public function update() {
        $sql = "
            UPDATE {$this->table}
            SET
              name         = :name,
              description  = :description,
              customer_id  = :customer_id,
              start_date   = :start_date,
              end_date     = :end_date,
              status       = :status,
              priority     = :priority,
              progress     = :progress,
              budget       = :budget
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);
        
        // Bind
        $stmt->bindParam(':id',           $this->id,         PDO::PARAM_INT);
        $stmt->bindParam(':name',         $this->name);
        $stmt->bindParam(':description',  $this->description);
        $stmt->bindParam(':customer_id',  $this->customer_id);
        $stmt->bindParam(':start_date',   $this->start_date);
        $stmt->bindParam(':end_date',     $this->end_date);
        $stmt->bindParam(':status',       $this->status);
        $stmt->bindParam(':priority',     $this->priority);
        $stmt->bindParam(':progress',     $this->progress);
        $stmt->bindParam(':budget',       $this->budget);
        
        return $stmt->execute();
    }
    
    /**
     * Elimina un proyecto y sus dependencias (tareas y miembros)
     */
    public function delete() {
        // Borrar miembros
        $this->clearMembers($this->id);
        // Borrar tareas
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE project_id = :pid");
        $stmt->bindParam(':pid', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        // Borrar proyecto
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /** Añade un miembro */
    public function addMember($project_id, $user_id, $role = '') {
        $stmt = $this->conn->prepare("
            INSERT INTO project_members (project_id, user_id, role)
            VALUES (:pid, :uid, :role)
        ");
        $stmt->bindParam(':pid',  $project_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid',  $user_id,    PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }
    
    /** Elimina todos los miembros */
    public function clearMembers($project_id) {
        $stmt = $this->conn->prepare("
            DELETE FROM project_members WHERE project_id = :pid
        ");
        $stmt->bindParam(':pid', $project_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /** Obtiene miembros del equipo */
    public function getTeamMembers($project_id) {
        $stmt = $this->conn->prepare("
            SELECT pm.*, u.full_name, u.email
            FROM project_members pm
            JOIN users u ON pm.user_id = u.id
            WHERE pm.project_id = :pid
        ");
        $stmt->bindParam(':pid', $project_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Formatea fechas, presupuesto y extrae iniciales de equipo
     */
    private function formatProjects(array $rows): array {
        foreach ($rows as &$project) {
            // Fecha
            if (!empty($project['start_date'])) {
                $project['start_date'] = date('d/m/Y', strtotime($project['start_date']));
            }
            if (!empty($project['end_date'])) {
                $project['end_date'] = date('d/m/Y', strtotime($project['end_date']));
            }
            // Presupuesto
            $project['budget_formatted'] = number_format($project['budget'], 2, ',', '.') . '€';
            // Extract initials for a quick display (si antes cargaste team members)
            if (isset($project['team']) && is_array($project['team'])) {
                $project['team_initials'] = array_map(function($m){
                    return strtoupper(substr($m['full_name'],0,1));
                }, $project['team']);
            }
        }
        return $rows;
    }
}
