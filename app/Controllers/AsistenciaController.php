<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class AsistenciaController extends Controller {
    private $asistenciaModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->asistenciaModel = $this->model('Asistencia');
    }

    public function index() {
        if (!SesionHelper::tienePermiso('asistencia', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        // Capturar filtros desde GET
        $filtros = [
            'usuario_id' => $_GET['usuario_id'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? ''
        ];

        // --- Validación de Horarios y Novedades ---
        $marcaciones = $this->asistenciaModel->obtenerMarcaciones($filtros);
        $this->asistenciaModel->procesarEstadosMarcaciones($marcaciones);
        // ------------------------------
        
        // Cargar lista de usuarios para el selector del filtro
        $db = new \app\Core\Database();
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $usuarios = $conn->query("SELECT id, nombre, cedula FROM usuarios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(\PDO::FETCH_OBJ);

        $data = [
            'titulo' => 'Módulo de Asistencia',
            'marcaciones' => $marcaciones,
            'usuarios' => $usuarios,
            'filtros' => $filtros
        ];

        $this->view('asistencia/index', $data);
    }

    public function editar($id) {
        if (!SesionHelper::tienePermiso('asistencia', 'editar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para editar marcaciones.";
            header('location: ' . URLROOT . '/asistencia');
            exit();
        }

        $data = [
            'titulo' => 'Editar Marcación',
            'marcacion' => $this->asistenciaModel->obtenerMarcacionPorId($id)
        ];
        $this->view('asistencia/editar', $data);
    }

    public function actualizar() {
        if (!SesionHelper::tienePermiso('asistencia', 'editar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para realizar esta acción.";
            header('location: ' . URLROOT . '/asistencia');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Unir fecha y hora separadas
            $fecha_hora = $_POST['fecha'] . ' ' . $_POST['hora'] . ':00';
            
            $data = [
                'id' => $_POST['id'],
                'tipo' => $_POST['tipo'],
                'registrado_en' => $fecha_hora
            ];
            
            if ($this->asistenciaModel->actualizarMarcacion($data)) {
                $_SESSION['mensaje_exito'] = "Marcación actualizada.";
            } else {
                $_SESSION['mensaje_error'] = "Error al actualizar.";
            }
            header('location: ' . URLROOT . '/asistencia');
        }
    }

    public function eliminar($id) {
        if (!SesionHelper::tienePermiso('asistencia', 'eliminar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para eliminar marcaciones.";
            header('location: ' . URLROOT . '/asistencia');
            exit();
        }

        $this->asistenciaModel->eliminarMarcacion($id);
        $_SESSION['mensaje_exito'] = "Marcación eliminada.";
        header('location: ' . URLROOT . '/asistencia');
    }
}
