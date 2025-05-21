<?php
class AuthController {
    private $db;
    private $user;
    
    public function __construct() {
        // Inicializar base de datos
        require_once 'config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Inicializar modelo de usuario
        require_once 'models/user.php';
        $this->user = new User($this->db);
    }
    
    // Método para el formulario de login
    public function login() {
        $error = '';
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar credenciales
            if(empty($_POST['email']) || empty($_POST['password'])) {
                $error = 'Por favor, ingrese email y contraseña';
            } else {
                $email = $_POST['email'];
                $password = $_POST['password'];
                
                // Intentar login
                if($this->user->login($email, $password)) {
                    // Iniciar sesión
                    session_start();
                    $_SESSION['user_id'] = $this->user->id;
                    $_SESSION['user_email'] = $this->user->email;
                    $_SESSION['user_name'] = $this->user->full_name;
                    $_SESSION['user_role'] = $this->user->role;
                    
                    // Redirigir al dashboard
                    header('Location: index.php?controller=dashboard');
                    exit;
                } else {
                    $error = 'Credenciales inválidas';
                }
            }
        }
        
        // Cargar vista
        include_once 'views/auth/login.php';
    }
    
    // Método para registro (sólo accesible a administradores)
    public function register() {
        // Verificar si el usuario está logueado y es admin
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        $error = '';
        $success = '';
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            if(empty($_POST['email']) || empty($_POST['password']) || empty($_POST['full_name']) || empty($_POST['role'])) {
                $error = 'Todos los campos son obligatorios';
            } else {
                // Configurar modelo
                $this->user->email = $_POST['email'];
                $this->user->password = $_POST['password'];
                $this->user->full_name = $_POST['full_name'];
                $this->user->role = $_POST['role'];
                $this->user->department = isset($_POST['department']) ? $_POST['department'] : '';
                $this->user->active = 1;
                
                // Intentar crear
                if($this->user->create()) {
                    $success = 'Usuario creado correctamente';
                } else {
                    $error = 'Error al crear el usuario. El email podría ya estar en uso.';
                }
            }
        }
        
        // Cargar vista
        include_once 'views/auth/register.php';
    }
    
    // Método para cerrar sesión
    public function logout() {
        session_start();
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir a login
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
    
    // Método para listar usuarios (sólo admin)
    public function users() {
        // Verificar si el usuario está logueado y es admin
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Obtener todos los usuarios
        $stmt = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cargar vista
        include_once 'views/auth/users.php';
    }
    
    // Método para editar usuario
    public function edit() {
        // Verificar si el usuario está logueado y es admin
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        $error = '';
        $success = '';
        $user_data = null;
        
        // Obtener ID del usuario a editar
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id > 0) {
            // Obtener datos del usuario
            $this->user->id = $id;
            if($this->user->read_single()) {
                $user_data = [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'full_name' => $this->user->full_name,
                    'role' => $this->user->role,
                    'department' => $this->user->department,
                    'active' => $this->user->active
                ];
            } else {
                $error = 'Usuario no encontrado';
            }
        } else {
            $error = 'ID de usuario inválido';
        }
        
        // Si el formulario fue enviado
        if($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            // Validar datos
            if(empty($_POST['email']) || empty($_POST['full_name']) || empty($_POST['role'])) {
                $error = 'Email, nombre y rol son obligatorios';
            } else {
                // Configurar modelo
                $this->user->id = $id;
                $this->user->email = $_POST['email'];
                $this->user->full_name = $_POST['full_name'];
                $this->user->role = $_POST['role'];
                $this->user->department = isset($_POST['department']) ? $_POST['department'] : '';
                $this->user->active = isset($_POST['active']) ? 1 : 0;
                
                // Si se proporcionó una nueva contraseña
                if(!empty($_POST['password'])) {
                    $this->user->password = $_POST['password'];
                }
                
                // Intentar actualizar
                if($this->user->update()) {
                    $success = 'Usuario actualizado correctamente';
                    
                    // Actualizar datos mostrados
                    $user_data = [
                        'id' => $this->user->id,
                        'email' => $this->user->email,
                        'full_name' => $this->user->full_name,
                        'role' => $this->user->role,
                        'department' => $this->user->department,
                        'active' => $this->user->active
                    ];
                } else {
                    $error = 'Error al actualizar el usuario. El email podría ya estar en uso.';
                }
            }
        }
        
        // Cargar vista
        include_once 'views/auth/edit.php';
    }
    
    // Método para eliminar usuario
    public function delete() {
        // Verificar si el usuario está logueado y es admin
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Obtener ID del usuario a eliminar
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // No permitir eliminar al propio usuario
        if($id === $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario';
            header('Location: index.php?controller=auth&action=users');
            exit;
        }
        
        if($id > 0) {
            // Configurar modelo
            $this->user->id = $id;
            
            // Intentar eliminar
            if($this->user->delete()) {
                $_SESSION['success'] = 'Usuario eliminado correctamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar el usuario';
            }
        } else {
            $_SESSION['error'] = 'ID de usuario inválido';
        }
        
        // Redirigir a la lista de usuarios
        header('Location: index.php?controller=auth&action=users');
        exit;
    }
}
?>