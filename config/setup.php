<?php
// Configuración de la base de datos
$host = "localhost";
$db_name = "mep_projects_db";
$username = "root";
$password = ""; // Cambia esto si has establecido una contraseña

try {
    // Crear conexión PDO
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos si no existe
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
    $conn->exec($sql);
    echo "Base de datos '$db_name' creada correctamente o ya existía.<br>";
    
    // Seleccionar la base de datos
    $conn->exec("USE $db_name");
    
    // Crear tabla users
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'manager', 'employee', 'client') NOT NULL DEFAULT 'employee',
        department VARCHAR(50),
        profile_image VARCHAR(255),
        active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )";
    $conn->exec($sql_users);
    echo "Tabla 'users' creada correctamente.<br>";
    
    // Crear tabla roles_permissions
    $sql_permissions = "CREATE TABLE IF NOT EXISTS roles_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role ENUM('admin', 'manager', 'employee', 'client') NOT NULL,
        module VARCHAR(50) NOT NULL,
        permission_read BOOLEAN DEFAULT FALSE,
        permission_write BOOLEAN DEFAULT FALSE,
        permission_delete BOOLEAN DEFAULT FALSE,
        permission_admin BOOLEAN DEFAULT FALSE,
        UNIQUE KEY role_module (role, module)
    )";
    $conn->exec($sql_permissions);
    echo "Tabla 'roles_permissions' creada correctamente.<br>";
    
    // Crear tabla projects
    $sql_projects = "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        client_id INT,
        start_date DATE,
        end_date DATE,
        status ENUM('iniciado', 'en_progreso', 'en_pausa', 'completado', 'cancelado') DEFAULT 'iniciado',
        priority ENUM('baja', 'media', 'alta') DEFAULT 'media',
        progress INT DEFAULT 0,
        budget DECIMAL(10,2),
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    )";
    $conn->exec($sql_projects);
    echo "Tabla 'projects' creada correctamente.<br>";
    
    // Crear tabla project_members
    $sql_project_members = "CREATE TABLE IF NOT EXISTS project_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        user_id INT NOT NULL,
        role VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY project_user (project_id, user_id)
    )";
    $conn->exec($sql_project_members);
    echo "Tabla 'project_members' creada correctamente.<br>";
    
    // Crear tabla tasks
    $sql_tasks = "CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        project_id INT NOT NULL,
        assigned_to INT,
        status ENUM('pendiente', 'en_progreso', 'completada', 'cancelada') DEFAULT 'pendiente',
        priority ENUM('baja', 'media', 'alta') DEFAULT 'media',
        start_date DATE,
        due_date DATE,
        completion_date DATE,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    )";
    $conn->exec($sql_tasks);
    echo "Tabla 'tasks' creada correctamente.<br>";
    
    // Crear tabla clients (para CRM)
    $sql_clients = "CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNIQUE,
        company_name VARCHAR(100),
        contact_name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        city VARCHAR(50),
        country VARCHAR(50),
        postal_code VARCHAR(20),
        website VARCHAR(100),
        notes TEXT,
        status ENUM('activo', 'inactivo', 'potencial') DEFAULT 'activo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $conn->exec($sql_clients);
    echo "Tabla 'clients' creada correctamente.<br>";
    
    // Verificar si ya hay usuarios en la base de datos
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($user_count == 0) {
        // Insertar usuario administrador
        $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (email, password, full_name, role) 
                        VALUES ('admin@mep-projects.com', :password, 'Administrador', 'admin')";
        $stmt = $conn->prepare($insert_admin);
        $stmt->bindParam(":password", $admin_password);
        $stmt->execute();
        
        echo "Usuario admin creado con:<br>";
        echo "Email: admin@mep-projects.com<br>";
        echo "Contraseña: admin123<br>";
        
        // Insertar permisos para administrador
        $modules = ['dashboard', 'projects', 'crm', 'erp', 'rrhh'];
        foreach ($modules as $module) {
            $insert_permission = "INSERT INTO roles_permissions (role, module, permission_read, permission_write, permission_delete, permission_admin) 
                                VALUES ('admin', :module, 1, 1, 1, 1)";
            $stmt = $conn->prepare($insert_permission);
            $stmt->bindParam(":module", $module);
            $stmt->execute();
        }
        
        echo "Permisos para administrador creados.<br>";
    } else {
        echo "Ya existen usuarios en la base de datos.<br>";
    }
    
    echo "<br>Configuración completada con éxito. <a href='../index.php'>Ir al inicio</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>