<?php
class Database {
    private $host = "localhost";
    private $db_name = "mep_projects_db";
    private $username = "root";
    private $password = ""; // Cambia esto si has establecido una contraseña
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>