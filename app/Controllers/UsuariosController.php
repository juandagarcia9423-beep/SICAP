<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class UsuariosController extends Controller {
    private $usuarioModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->usuarioModel = $this->model('Usuario');
    }

    public function index() {
        if (!SesionHelper::tienePermiso('usuarios', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $data = [
            'titulo' => 'Gestión de Usuarios',
            'usuarios' => $this->getUsersFromDB()
        ];
        $this->view('usuarios/index', $data);
    }

    public function crear() {
        if (!SesionHelper::tienePermiso('usuarios', 'crear')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para crear usuarios.";
            header('location: ' . URLROOT . '/usuarios/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre']),
                'usuario' => trim($_POST['usuario']),
                'cedula' => trim($_POST['cedula']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'rol' => trim($_POST['rol']),
                'area' => trim($_POST['area']),
                'tipo_jornada' => trim($_POST['tipo_jornada']),
                'permite_pin' => isset($_POST['permite_pin']) ? 1 : 0,
                'permite_facial' => isset($_POST['permite_facial']) ? 1 : 0,
                'permite_qr' => isset($_POST['permite_qr']) ? 1 : 0,
                'pin_secreto' => trim($_POST['pin_secreto']),
                'foto_facial' => $_POST['foto_facial'] ?? null,
                'error' => ''
            ];

            if (empty($data['usuario']) || empty($data['password'])) {
                $data['error'] = 'Por favor complete todos los campos obligatorios.';
                $this->view('usuarios/crear', $data);
            } else {
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($this->usuarioModel->registrar($data)) {
                    $_SESSION['mensaje_exito'] = '¡Usuario registrado correctamente!';
                    header('location: ' . URLROOT . '/usuarios/index');
                    exit();
                } else {
                    $data['error'] = 'Algo salió mal al registrar el usuario.';
                    $this->view('usuarios/crear', $data);
                }
            }
        } else {
            $data = [
                'titulo' => 'Registrar Nuevo Usuario',
                'error' => ''
            ];
            $this->view('usuarios/crear', $data);
        }
    }

    public function editar($id) {
        if (!SesionHelper::tienePermiso('usuarios', 'editar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para editar usuarios.";
            header('location: ' . URLROOT . '/usuarios/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'nombre' => trim($_POST['nombre']),
                'usuario' => trim($_POST['usuario']),
                'cedula' => trim($_POST['cedula']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']),
                'area' => trim($_POST['area']),
                'tipo_jornada' => trim($_POST['tipo_jornada']),
                'permite_pin' => isset($_POST['permite_pin']) ? 1 : 0,
                'permite_facial' => isset($_POST['permite_facial']) ? 1 : 0,
                'permite_qr' => isset($_POST['permite_qr']) ? 1 : 0,
                'pin_secreto' => trim($_POST['pin_secreto']),
                'foto_facial' => $_POST['foto_facial'] ?? null,
                'password_hash' => !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '',
                'error' => ''
            ];

            if ($this->usuarioModel->actualizar($data)) {
                $_SESSION['mensaje_exito'] = '¡Usuario actualizado con éxito!';
                header('location: ' . URLROOT . '/usuarios/index');
                exit();
            } else {
                $data['error'] = 'Error al actualizar el usuario.';
                $this->view('usuarios/editar', $data);
            }
        } else {
            $usuario = $this->usuarioModel->obtenerUsuarioPorId($id);
            if (!$usuario) {
                header('location: ' . URLROOT . '/usuarios/index');
                exit();
            }
            $data = [
                'titulo' => 'Editar Usuario',
                'usuario' => $usuario,
                'error' => ''
            ];
            $this->view('usuarios/editar', $data);
        }
    }

    public function eliminar($id) {
        if (!SesionHelper::tienePermiso('usuarios', 'eliminar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para eliminar usuarios.";
            header('location: ' . URLROOT . '/usuarios/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->usuarioModel->eliminar($id)) {
                $_SESSION['mensaje_exito'] = 'El usuario ha sido eliminado.';
                header('location: ' . URLROOT . '/usuarios/index');
                exit();
            } else {
                die('Error al eliminar el usuario');
            }
        } else {
            header('location: ' . URLROOT . '/usuarios/index');
            exit();
        }
    }

    private function getUsersFromDB() {
        $db = new \app\Core\Database();
        $db->query("SELECT * FROM usuarios ORDER BY creado_en DESC");
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        return $conn->query("SELECT * FROM usuarios ORDER BY creado_en DESC")->fetchAll(\PDO::FETCH_OBJ);
    }
}
