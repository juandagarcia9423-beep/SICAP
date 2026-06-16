<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class PerfilController extends Controller {
    private $usuarioModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->usuarioModel = $this->model('Usuario');
    }

    public function index() {
        $usuario = $this->usuarioModel->obtenerUsuarioPorId($_SESSION['usuario_id']);
        
        $data = [
            'titulo' => 'Mi Perfil',
            'usuario' => $usuario,
            'error' => ''
        ];

        $this->view('perfil/index', $data);
    }

    public function cambiarPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $_SESSION['usuario_id'],
                'password_actual' => trim($_POST['password_actual']),
                'password_nueva' => trim($_POST['password_nueva']),
                'password_confirmar' => trim($_POST['password_confirmar']),
                'error' => ''
            ];

            $usuario = $this->usuarioModel->obtenerUsuarioPorId($data['id']);

            if (!password_verify($data['password_actual'], $usuario->password_hash)) {
                $data['error'] = 'La contraseña actual es incorrecta.';
            } elseif ($data['password_nueva'] != $data['password_confirmar']) {
                $data['error'] = 'Las nuevas contraseñas no coinciden.';
            } elseif (strlen($data['password_nueva']) < 6) {
                $data['error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
            }

            if (empty($data['error'])) {
                $updateData = [
                    'id' => $data['id'],
                    'password_hash' => password_hash($data['password_nueva'], PASSWORD_DEFAULT),
                    // Necesitamos pasar el resto de los datos para el método actualizar del modelo
                    'nombre' => $usuario->nombre,
                    'usuario' => $usuario->usuario,
                    'cedula' => $usuario->cedula,
                    'email' => $usuario->email,
                    'rol' => $usuario->rol,
                    'area' => $usuario->area,
                    'tipo_jornada' => $usuario->tipo_jornada,
                    'tipo_personal' => $usuario->tipo_personal,
                    'permite_pin' => $usuario->permite_pin,
                    'permite_facial' => $usuario->permite_facial,
                    'permite_qr' => $usuario->permite_qr,
                    'pin_secreto' => $usuario->pin_secreto,
                    'foto_facial' => $usuario->foto_facial,
                    'dias_cambio_password' => $usuario->dias_cambio_password,
                    'alerta_cambio_password' => $usuario->alerta_cambio_password
                ];

                if ($this->usuarioModel->actualizar($updateData)) {
                    $_SESSION['mensaje_exito'] = '¡Contraseña actualizada correctamente!';
                    header('location: ' . URLROOT . '/perfil/index');
                    exit();
                } else {
                    $data['error'] = 'Error al actualizar la contraseña.';
                }
            }

            $data['titulo'] = 'Mi Perfil';
            $data['usuario'] = $usuario;
            $this->view('perfil/index', $data);
        } else {
            header('location: ' . URLROOT . '/perfil/index');
        }
    }
}
