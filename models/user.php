<?php
class User {
    private $conn;
    private $table = 'users';
    
    // Propiedades del usuario
    public $id;
    public $email;
    public $password;
    public $full_name;
    public $role;
    public $department;
    public $profile_image;
    public $active;
    public $created_at;
    public $updated_at;
    public $last_login;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Obtener usuario por ID
    public function read_single() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->email = $row['email'];
            $this->full_name = $row['full_name'];
            $this->role = $row['role'];
            $this->department = $row['department'];
            $this->profile_image = $row['profile_image'];
            $this->active = $row['active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->last_login = $row['last_login'];
            return true;
        }
        
        return false;
    }
    
    // Login: verificar credenciales
    public function login($email, $password) {
        $query = "SELECT id, email, password, full_name, role FROM " . $this->table . " 
                 WHERE email = :email AND active = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if(password_verify($password, $row['password'])) {
                // Actualizar último login
                $update_query = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = :id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':id', $row['id']);
                $update_stmt->execute();
                
                // Establecer propiedades
                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->full_name = $row['full_name'];
                $this->role = $row['role'];
                
                return true;
            }
        }
        
        return false;
    }
    
    // Crear un nuevo usuario
    public function create() {
        // Verificar si el email ya existe
        $check_query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':email', $this->email);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            return false; // Email ya existe
        }
        
        // Hash de la contraseña
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . "
                 (email, password, full_name, role, department, active)
                 VALUES
                 (:email, :password, :full_name, :role, :department, :active)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitización
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->department = htmlspecialchars(strip_tags($this->department));
        
        // Valor por defecto para active
        $active = $this->active ?? 1;
        
        // Binding
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':active', $active);
        
        // Ejecutar
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Leer todos los usuarios
    public function read() {
        $query = "SELECT id, email, full_name, role, department, active, created_at, last_login
                 FROM " . $this->table . " ORDER BY full_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Actualizar usuario
    public function update() {
        // Verificar si el email ya existe (excluyendo este usuario)
        if(!empty($this->email)) {
            $check_query = "SELECT id FROM " . $this->table . " WHERE email = :email AND id != :id LIMIT 1";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':email', $this->email);
            $check_stmt->bindParam(':id', $this->id);
            $check_stmt->execute();
            
            if($check_stmt->rowCount() > 0) {
                return false; // Email ya existe
            }
        }
        
        // Si la contraseña se está actualizando
        if(!empty($this->password)) {
            $query = "UPDATE " . $this->table . "
                     SET email = :email, 
                         password = :password,
                         full_name = :full_name,
                         role = :role,
                         department = :department,
                         active = :active
                     WHERE id = :id";
                     
            $stmt = $this->conn->prepare($query);
            
            // Hash la contraseña
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        } else {
            $query = "UPDATE " . $this->table . "
                     SET email = :email,
                         full_name = :full_name,
                         role = :role,
                         department = :department,
                         active = :active
                     WHERE id = :id";
                     
            $stmt = $this->conn->prepare($query);
        }
        
        // Sanitización
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->department = htmlspecialchars(strip_tags($this->department));
        
        // Binding
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':department', $this->department);
        $stmt->bindParam(':active', $this->active);
        
        // Ejecutar
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Eliminar usuario
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitización
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Binding
        $stmt->bindParam(':id', $this->id);
        
        // Ejecutar
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Verificar si el usuario tiene permiso para un módulo
    public function hasPermission($module, $permission_type = 'read') {
        // Si es administrador, tiene todos los permisos
        if($this->role === 'admin') {
            return true;
        }
        
        $query = "SELECT * FROM roles_permissions 
                 WHERE role = :role AND module = :module";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':module', $module);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            switch($permission_type) {
                case 'read':
                    return (bool)$row['permission_read'];
                case 'write':
                    return (bool)$row['permission_write'];
                case 'delete':
                    return (bool)$row['permission_delete'];
                case 'admin':
                    return (bool)$row['permission_admin'];
                default:
                    return false;
            }
        }
        
        return false;
    }
}
?>