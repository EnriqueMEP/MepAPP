<?php
// models/client.php

class Client {
    private $conn;
    private $table = 'customers';

    // Propiedades expuestas a las vistas
    public $id;
    public $company_name; // mapeado desde name
    public $email;
    public $phone;
    public $status;       // mapeado desde active
    public $created_at;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Devuelve todos los clientes (customers)
     */
    public function read() {
        $sql = "
            SELECT
                c.id,
                c.name   AS company_name,
                c.email,
                c.phone,
                c.active AS status,
                c.created_at
            FROM {$this->table} c
            ORDER BY c.name
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve un cliente por su ID
     */
    public function read_single() {
        $sql = "
            SELECT
                c.id,
                c.name   AS company_name,
                c.email,
                c.phone,
                c.active AS status,
                c.created_at
            FROM {$this->table} c
            WHERE c.id = :id
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un nuevo cliente
     */
    public function create() {
        $sql = "
            INSERT INTO {$this->table}
              (name, email, phone, active)
            VALUES
              (:name, :email, :phone, :active)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name',   $this->company_name);
        $stmt->bindParam(':email',  $this->email);
        $stmt->bindParam(':phone',  $this->phone);
        $stmt->bindParam(':active', $this->status, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $this->id = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Actualiza un cliente existente
     */
    public function update() {
        $sql = "
            UPDATE {$this->table}
            SET
              name   = :name,
              email  = :email,
              phone  = :phone,
              active = :active
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id',     $this->id,        PDO::PARAM_INT);
        $stmt->bindParam(':name',   $this->company_name);
        $stmt->bindParam(':email',  $this->email);
        $stmt->bindParam(':phone',  $this->phone);
        $stmt->bindParam(':active', $this->status,    PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Elimina un cliente
     */
    public function delete() {
        $stmt = $this->conn->prepare("
            DELETE FROM {$this->table} WHERE id = :id
        ");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Busca clientes por palabra clave
     */
    public function search($keyword) {
        $sql = "
            SELECT
                c.id,
                c.name   AS company_name,
                c.email,
                c.phone,
                c.active AS status,
                c.created_at
            FROM {$this->table} c
            WHERE c.name  LIKE :kw
               OR c.email LIKE :kw
               OR c.phone LIKE :kw
            ORDER BY c.name
        ";
        $stmt = $this->conn->prepare($sql);
        $kw = "%{$keyword}%";
        $stmt->bindParam(':kw', $kw);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
