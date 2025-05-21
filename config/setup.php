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
    
    // Crear tabla messages (para Chat)
    $sql_messages = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        recipient_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX (sender_id),
        INDEX (recipient_id),
        INDEX (created_at)
    )";
    $conn->exec($sql_messages);
    echo "Tabla 'messages' creada correctamente.<br>";
    
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
        $modules = ['dashboard', 'projects', 'crm', 'erp', 'rrhh', 'chat'];
        foreach ($modules as $module) {
            $insert_permission = "INSERT INTO roles_permissions (role, module, permission_read, permission_write, permission_delete, permission_admin) 
                                VALUES ('admin', :module, 1, 1, 1, 1)";
            $stmt = $conn->prepare($insert_permission);
            $stmt->bindParam(":module", $module);
            $stmt->execute();
        }
        
        // Insertar permisos para otros roles
        $roles = ['manager', 'employee', 'client'];
        foreach ($roles as $role) {
            foreach ($modules as $module) {
                // Por defecto, todos los roles pueden leer y escribir en el chat
                $read = 1;
                $write = ($module == 'chat') ? 1 : 0;
                $delete = 0;
                $admin = 0;
                
                // Managers tienen más permisos
                if ($role == 'manager') {
                    $write = 1;
                    $delete = ($module == 'chat') ? 0 : 1;
                }
                
                $insert_permission = "INSERT INTO roles_permissions (role, module, permission_read, permission_write, permission_delete, permission_admin) 
                                    VALUES (:role, :module, :read, :write, :delete, :admin)";
                $stmt = $conn->prepare($insert_permission);
                $stmt->bindParam(":role", $role);
                $stmt->bindParam(":module", $module);
                $stmt->bindParam(":read", $read, PDO::PARAM_INT);
                $stmt->bindParam(":write", $write, PDO::PARAM_INT);
                $stmt->bindParam(":delete", $delete, PDO::PARAM_INT);
                $stmt->bindParam(":admin", $admin, PDO::PARAM_INT);
                
                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    // Ignorar si ya existe
                    if ($e->getCode() != 23000) { // No es error de duplicado
                        throw $e;
                    }
                }
            }
        }
        
        echo "Permisos para todos los roles creados.<br>";
        
        // Insertar algunos usuarios de ejemplo para poder probar el chat
        $users = [
            ['ana.lopez@mep-projects.com', 'Ana López', 'employee', 'Desarrollo'],
            ['carlos.gomez@mep-projects.com', 'Carlos Gómez', 'employee', 'Diseño'],
            ['maria.rodriguez@mep-projects.com', 'María Rodríguez', 'manager', 'Proyectos'],
            ['pedro.sanchez@mep-projects.com', 'Pedro Sánchez', 'employee', 'Desarrollo']
        ];
        
        $password = password_hash("usuario123", PASSWORD_DEFAULT);
        
        $insert_user = "INSERT INTO users (email, password, full_name, role, department) 
                      VALUES (:email, :password, :full_name, :role, :department)";
        $stmt = $conn->prepare($insert_user);
        
        foreach ($users as $user) {
            $stmt->bindParam(":email", $user[0]);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":full_name", $user[1]);
            $stmt->bindParam(":role", $user[2]);
            $stmt->bindParam(":department", $user[3]);
            $stmt->execute();
        }
        
        echo "Usuarios de ejemplo creados con contraseña: usuario123<br>";
        
        // Insertar algunos mensajes de ejemplo para el chat
        $admin_id = $conn->lastInsertId() - 4; // ID del admin
        $ana_id = $admin_id + 1;
        $carlos_id = $admin_id + 2;
        
        $messages = [
            [$ana_id, $admin_id, "Hola, ¿cómo estás?", date('Y-m-d H:i:s', strtotime('-2 days')), 1],
            [$admin_id, $ana_id, "Hola! Todo bien, trabajando en el proyecto de MEP-2025. ¿Y tú?", date('Y-m-d H:i:s', strtotime('-2 days +1 hour')), 1],
            [$ana_id, $admin_id, "Bien, gracias. Estoy revisando los documentos que me enviaste ayer.", date('Y-m-d H:i:s', strtotime('-1 day')), 1],
            [$carlos_id, $admin_id, "Ya te envié los archivos por correo", date('Y-m-d H:i:s', strtotime('-1 day +2 hours')), 1],
        ];
        
        $insert_message = "INSERT INTO messages (sender_id, recipient_id, content, created_at, is_read) 
                         VALUES (:sender_id, :recipient_id, :content, :created_at, :is_read)";
        $stmt = $conn->prepare($insert_message);
        
        foreach ($messages as $message) {
            $stmt->bindParam(":sender_id", $message[0]);
            $stmt->bindParam(":recipient_id", $message[1]);
            $stmt->bindParam(":content", $message[2]);
            $stmt->bindParam(":created_at", $message[3]);
            $stmt->bindParam(":is_read", $message[4]);
            $stmt->execute();
        }
        
        echo "Mensajes de ejemplo creados para el chat.<br>";
    } else {
        echo "Ya existen usuarios en la base de datos.<br>";
        
        // Comprobar si ya existe el módulo chat en roles_permissions
        $check_chat = "SELECT COUNT(*) as count FROM roles_permissions WHERE module = 'chat'";
        $stmt = $conn->query($check_chat);
        $chat_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($chat_count == 0) {
            // Insertar permisos para el módulo chat para todos los roles
            $roles = ['admin', 'manager', 'employee', 'client'];
            foreach ($roles as $role) {
                $read = 1;
                $write = 1;
                $delete = ($role == 'admin') ? 1 : 0;
                $admin = ($role == 'admin') ? 1 : 0;
                
                $insert_permission = "INSERT INTO roles_permissions (role, module, permission_read, permission_write, permission_delete, permission_admin) 
                                    VALUES (:role, 'chat', :read, :write, :delete, :admin)";
                $stmt = $conn->prepare($insert_permission);
                $stmt->bindParam(":role", $role);
                $stmt->bindParam(":read", $read, PDO::PARAM_INT);
                $stmt->bindParam(":write", $write, PDO::PARAM_INT);
                $stmt->bindParam(":delete", $delete, PDO::PARAM_INT);
                $stmt->bindParam(":admin", $admin, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            echo "Permisos para el módulo chat creados.<br>";
        }
    }
    
    echo "<br>Configuración completada con éxito. <a href='../index.php'>Ir al inicio</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>