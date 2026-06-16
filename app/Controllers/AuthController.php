<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class AuthController extends Controller {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    public function index() {
        $this->login();
    }

    public function login() {
        SesionHelper::init();
        // Si ya está logueado, redirigir a usuarios
        if (SesionHelper::estaLogueado()) {
            header('location: ' . URLROOT . '/usuarios');
            exit();
        }

        $db = new \app\Core\Database();
        $db->query("SELECT * FROM configuracion_seguridad");
        $configs = $db->resultSet();
        $authConfig = [];
        foreach($configs as $c) $authConfig[$c->clave] = (bool)$c->valor;

        // DEBUG: Uncomment to check values
        // error_log("Auth Config: " . print_r($authConfig, true));

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar formulario
            $data = [
                'identificador' => trim($_POST['usuario']),
                'password' => trim($_POST['password']),
                'error' => '',
                'authConfig' => $authConfig
            ];

            $usuarioLogueado = $this->usuarioModel->login($data['identificador'], $data['password']);

            if ($usuarioLogueado) {
                // Crear sesión mediante el Helper
                SesionHelper::init();
                $_SESSION['usuario_id'] = $usuarioLogueado->id;
                $_SESSION['usuario_nombre'] = $usuarioLogueado->nombre;
                $_SESSION['usuario_rol'] = $usuarioLogueado->rol;
                
                header('location: ' . URLROOT . '/dashboard/index');
                exit();
            } else {
                $data['error'] = 'Usuario o contraseña incorrectos';
                $this->view('auth/login', $data);
            }

        } else {
            // Cargar vista de login
            $data = [
                'identificador' => '',
                'password' => '',
                'error' => '',
                'authConfig' => $authConfig
            ];
            $this->view('auth/login', $data);
        }
    }

    public function pin() {
        SesionHelper::init();
        $this->view('auth/pin', ['titulo' => 'Acceso por PIN']);
    }

    public function facial() {
        $this->view('auth/facial', ['titulo' => 'Acceso Facial']);
    }

    public function qr() {
        $this->view('auth/qr', ['titulo' => 'Acceso por QR']);
    }

    public function metodos() {
        SesionHelper::init();
        $db = new \app\Core\Database();
        $db->query("SELECT * FROM configuracion_seguridad");
        $configs = $db->resultSet();
        $authConfig = [];
        foreach($configs as $c) $authConfig[$c->clave] = (bool)$c->valor;

        $this->view('auth/seleccion_metodo', ['authConfig' => $authConfig]);
    }

    public function get_user_facial($cedula) {
        $db = new \app\Core\Database();
        $db->query("SELECT id, nombre, foto_facial FROM usuarios WHERE cedula = :cedula AND permite_facial = 1");
        $db->bind(':cedula', $cedula);
        $usuario = $db->single();
        
        header('Content-Type: application/json');
        echo json_encode($usuario);
    }

    public function validar_facial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            SesionHelper::init();
            $usuario_id = $_POST['usuario_id'];

            $db = new \app\Core\Database();
            $db->query("SELECT * FROM usuarios WHERE id = :id AND permite_facial = 1");
            $db->bind(':id', $usuario_id);
            $usuario = $db->single();

            if ($usuario) {
                // Verificar última marcación para saber si es entrada o salida
                $db->query("SELECT tipo FROM asistencia WHERE usuario_id = :uid AND DATE(registrado_en) = CURDATE() ORDER BY registrado_en DESC LIMIT 1");
                $db->bind(':uid', $usuario->id);
                $ultimaMarcacion = $db->single();
                
                $data = [
                    'titulo' => 'Marcar Asistencia',
                    'usuario' => $usuario,
                    'ultimaMarcacion' => $ultimaMarcacion
                ];
                $this->view('auth/marcar_asistencia', $data);
            } else {
                $_SESSION['mensaje_error'] = "Error de autenticación facial.";
                header('location: ' . URLROOT . '/auth/facial');
                exit();
            }
        }
    }

    public function validar_pin() {
        SesionHelper::init();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cedula = trim($_POST['cedula']);
            $pin = trim($_POST['pin']);
            
            // error_log("PIN Auth Attempt: Cedula: $cedula, PIN: $pin");

            $db = new \app\Core\Database();
            $db->query("SELECT * FROM usuarios WHERE cedula = :cedula AND pin_secreto = :pin AND permite_pin = 1");
            $db->bind(':cedula', $cedula);
            $db->bind(':pin', $pin);
            $usuario = $db->single();
            
            if ($usuario) {
                // error_log("Usuario encontrado: " . $usuario->nombre);
                
                // Verificar última marcación para saber si es entrada o salida
                $db->query("SELECT tipo FROM asistencia WHERE usuario_id = :uid AND DATE(registrado_en) = CURDATE() ORDER BY registrado_en DESC LIMIT 1");
                $db->bind(':uid', $usuario->id);
                $ultimaMarcacion = $db->single();
                
                $data = [
                    'titulo' => 'Marcar Asistencia',
                    'usuario' => $usuario,
                    'ultimaMarcacion' => $ultimaMarcacion
                ];
                $this->view('auth/marcar_asistencia', $data);
                return; // Importante detener aquí
            } else {
                // error_log("PIN Auth Failed: Cedula: $cedula");
                $_SESSION['mensaje_error'] = "Cédula o PIN incorrectos, o no tiene permiso.";
                header('location: ' . URLROOT . '/auth/pin');
                exit();
            }
        }
    }

    public function registrar_marcacion() {
        SesionHelper::init();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'];
            $tipo = $_POST['tipo']; // 'entrada' o 'salida'
            
            $asistenciaModel = $this->model('Asistencia');
            if ($asistenciaModel->registrarEvento($usuario_id, $tipo)) {
                $fechaHora = date('d/m/Y h:i:s A');
                $_SESSION['mensaje_exito'] = "Marcación de {$tipo} registrada con éxito.<br><br><small>{$fechaHora}</small>";
                
                // Forzar ejecución de automatización al instante para generar alertas si es necesario
                \app\Helpers\AutomacionHelper::ejecutarTareasCron();
            } else {
                $_SESSION['mensaje_error'] = "Error al registrar.";
            }
            header('location: ' . URLROOT . '/auth/metodos');
        }
    }

    public function logout() {
        SesionHelper::init();
        $_SESSION = [];
        session_destroy();
        header('location: ' . URLROOT . '/auth/login');
        exit();
    }
}
