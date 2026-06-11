<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class AuthController extends Controller {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    public function login() {
        // Si ya está logueado, redirigir a usuarios
        if (SesionHelper::estaLogueado()) {
            header('location: ' . URLROOT . '/usuarios');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar formulario
            $data = [
                'identificador' => trim($_POST['usuario']),
                'password' => trim($_POST['password']),
                'error' => ''
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
                'error' => ''
            ];
            $this->view('auth/login', $data);
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
