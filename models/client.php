<?php
// models/client.php
class Client {
    private $conn;
    private $table = 'clients';
    
    // Propiedades del cliente
    public $id;
    public $user_id;
    public $company_name;
    public $contact_name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $country;
    public $postal_code;
    public $website;
    public $notes;
    public $status;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Obtener todos los clientes
    public function read() {
        $query = "SELECT c.*, u.email as user_email, u.full_name as user_name
                 FROM " . $this->table . " c
                 LEFT JOIN users u ON c.user_id = u.id
                 ORDER BY c.company_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener un cliente específico
    public function read_single() {
        $query = "SELECT c.*, u.email as user_email, u.full_name as user_name
                 FROM " . $this->table . " c
                 LEFT JOIN users u ON c.user_id = u.id
                 WHERE c.id = :id
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->user_id = $row['user_id'];
            $this->company_name = $row['company_name'];
            $this->contact_name = $row['contact_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->country = $row['country'];
            $this->postal_code = $row['postal_code'];
            $this->website = $row['website'];
            $this->notes = $row['notes'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
        
        return $row;
    }
    
    // Crear un nuevo cliente
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                 (user_id, company_name, contact_name, email, phone, address, city, country, postal_code, website, notes, status)
                 VALUES
                 (:user_id, :company_name, :contact_name, :email, :phone, :address, :city, :country, :postal_code, :website, :notes, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->company_name = htmlspecialchars(strip_tags($this->company_name));
        $this->contact_name = htmlspecialchars(strip_tags($this->contact_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->country = htmlspecialchars(strip_tags($this->country));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->website = htmlspecialchars(strip_tags($this->website));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Vincular parámetros
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':company_name', $this->company_name);
        $stmt->bindParam(':contact_name', $this->contact_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':website', $this->website);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':status', $this->status);
        
        // Ejecutar consulta
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Actualizar un cliente existente
    public function update() {
        $query = "UPDATE " . $this->table . "
                 SET user_id = :user_id,
                     company_name = :company_name,
                     contact_name = :contact_name,
                     email = :email,
                     phone = :phone,
                     address = :address,
                     city = :city,
                     country = :country,
                     postal_code = :postal_code,
                     website = :website,
                     notes = :notes,
                     status = :status
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->company_name = htmlspecialchars(strip_tags($this->company_name));
        $this->contact_name = htmlspecialchars(strip_tags($this->contact_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->country = htmlspecialchars(strip_tags($this->country));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->website = htmlspecialchars(strip_tags($this->website));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Vincular parámetros
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':company_name', $this->company_name);
        $stmt->bindParam(':contact_name', $this->contact_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':website', $this->website);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':status', $this->status);
        
        // Ejecutar consulta
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Eliminar un cliente
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Buscar clientes
    public function search($keyword) {
        $query = "SELECT c.*, u.email as user_email, u.full_name as user_name
                 FROM " . $this->table . " c
                 LEFT JOIN users u ON c.user_id = u.id
                 WHERE c.company_name LIKE :keyword 
                    OR c.contact_name LIKE :keyword 
                    OR c.email LIKE :keyword 
                    OR c.city LIKE :keyword
                 ORDER BY c.company_name";
        
        $keyword = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener proyectos de un cliente
    public function getProjects() {
        require_once 'models/project.php';
        $project = new Project($this->conn);
        
        return $project->read('', $this->id, '', '');
    }
}
?>