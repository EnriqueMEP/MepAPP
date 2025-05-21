<?php
// controllers/auth_controller.php
require_once __DIR__ . '/base_controller.php';

class AuthController extends BaseController {
    protected $user;

    public function __construct() {
        parent::__construct();
        // Inicializar modelo de usuario
        require_once __DIR__ . '/../models/user.php';
        $this->user = new User($this->db);
        // La sesión se inicia en el front-controller (index.php)
    }

    /**
     * Mostrar formulario de login y procesar envío
     */
public function login() {
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $error = 'Por favor, ingrese email y contraseña';
        } else {
            // 1) Traer el hash desde la columna correcta (aquí 'password')
            $stmt = $this->db->prepare(
                "SELECT id, email, password, full_name, role
                   FROM users
                  WHERE email = ?"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2) Verificar que exista y coincida la contraseña
            if ($user && password_verify($password, $user['password'])) {
                // 3) Guardar datos en sesión
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name']  = $user['full_name'];
                $_SESSION['user_role']  = $user['role'];

                // 4) Redirigir al dashboard
                header('Location: index.php?controller=dashboard&action=index');
                exit;
            } else {
                $error = 'Credenciales inválidas';
            }
        }
    }

    // Mostrar el formulario de login personalizado
    require_once __DIR__ . '/../views/auth/login.php';
}


    /**
     * Registrar nuevo usuario (solo admins)
     */
    public function register() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $required = ['email', 'password', 'full_name', 'role'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    $error = 'Todos los campos marcados son obligatorios';
                    break;
                }
            }

            if (!$error) {
                $this->user->email      = trim($_POST['email']);
                $this->user->password   = trim($_POST['password']);
                $this->user->full_name  = trim($_POST['full_name']);
                $this->user->role       = trim($_POST['role']);
                $this->user->department = trim($_POST['department'] ?? '');
                $this->user->active     = 1;

                if ($this->user->create()) {
                    $success = 'Usuario creado correctamente';
                } else {
                    $error = 'Error al crear el usuario. El correo podría estar en uso.';
                }
            }
        }

        $this->render(
            'auth/register',
            ['error' => $error, 'success' => $success, 'title' => 'Registrar Usuario'],
            'layout_auth'
        );
    }

    /** Cerrar sesión */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    /** Listar usuarios (solo admins) */
    public function users() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $stmt  = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('auth/users', ['users' => $users, 'title' => 'Gestión de Usuarios']);
    }

    /** Editar usuario (solo admins) */
    public function edit() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $error     = '';
        $success   = '';
        $user_data = null;
        $id        = intval($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->user->id = $id;
            if ($this->user->read_single()) {
                $user_data = [
                    'id'        => $this->user->id,
                    'email'     => $this->user->email,
                    'full_name' => $this->user->full_name,
                    'role'      => $this->user->role,
                    'department'=> $this->user->department,
                    'active'    => $this->user->active
                ];
            } else {
                $error = 'Usuario no encontrado';
            }
        } else {
            $error = 'ID de usuario inválido';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            if (empty($_POST['email']) || empty($_POST['full_name']) || empty($_POST['role'])) {
                $error = 'Email, Nombre y Rol son obligatorios';
            } else {
                $this->user->id         = $id;
                $this->user->email      = trim($_POST['email']);
                $this->user->full_name  = trim($_POST['full_name']);
                $this->user->role       = trim($_POST['role']);
                $this->user->department = trim($_POST['department'] ?? '');
                $this->user->active     = isset($_POST['active']) ? 1 : 0;

                if (!empty($_POST['password'])) {
                    $this->user->password = trim($_POST['password']);
                }

                if ($this->user->update()) {
                    $success = 'Usuario actualizado correctamente';
                    $user_data['email']     = $this->user->email;
                    $user_data['full_name'] = $this->user->full_name;
                    $user_data['role']      = $this->user->role;
                    $user_data['department']= $this->user->department;
                    $user_data['active']    = $this->user->active;
                } else {
                    $error = 'Error al actualizar. El correo podría estar en uso.';
                }
            }
        }

        $this->render('auth/edit', ['error' => $error, 'success' => $success, 'user_data' => $user_data, 'title' => 'Editar Usuario']);
    }

    /** Eliminar usuario (solo admins) */
    public function delete() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $id = intval($_GET['id'] ?? 0);
        if ($id === $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario';
        } elseif ($id > 0) {
            $this->user->id = $id;
            if ($this->user->delete()) {
                $_SESSION['success'] = 'Usuario eliminado correctamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar usuario';
            }
        } else {
            $_SESSION['error'] = 'ID de usuario inválido';
        }

        header('Location: index.php?controller=auth&action=users');
        exit;
    }
}
