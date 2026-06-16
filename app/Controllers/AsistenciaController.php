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
        $alertaModel = $this->model('Alerta');
        
        $marcaciones = $this->asistenciaModel->obtenerMarcaciones($filtros);
        $alertaModel = $this->model('Alerta');
        
        // --- Validación de Horarios y Novedades ---

        foreach ($marcaciones as &$m) {
            $raw_timestamp = $m->registrado_en; 
            $parts = explode(' ', $raw_timestamp);
            $fecha_str = $parts[0];
            $hora_str = $parts[1]; // "15:00:55"

            // Convertir fecha de Y-m-d a d/m/Y manualmente
            $fecha_parts = explode('-', $fecha_str);
            $fecha_format = $fecha_parts[2] . '/' . $fecha_parts[1] . '/' . $fecha_parts[0];

            $dia_semana = (int)date('N', strtotime($fecha_str));
            $horario = $this->asistenciaModel->obtenerTurnoParaUsuario($m->usuario_id, $dia_semana);
            
            // Verificar permisos aprobados para hoy
            $db = new \app\Core\Database();
            $db->query("SELECT COUNT(*) FROM solicitudes_permiso 
                        WHERE usuario_id = :usuario_id AND estado = 'aprobada' AND DATE(fecha_permiso) = :fecha");
            $db->bind(':usuario_id', $m->usuario_id);
            $db->bind(':fecha', $fecha_str);
            $tienePermiso = $db->fetchColumn() > 0;

            $m->estado_marcacion = 'A Tiempo';

            if ($horario) {
                // Cálculo de tiempo en segundos
                $hora_marcacion = strtotime($hora_str);
                $hora_entrada = strtotime($horario->hora_entrada);
                $hora_salida = strtotime($horario->hora_salida);
                
                // Permitir 10 minutos de tolerancia (600 segundos)
                $tolerancia = 600;

                if ($m->tipo == 'entrada') {
                    if ($hora_str > $horario->hora_entrada && !$tienePermiso) {
                        $m->estado_marcacion = 'Tarde';
                    }
                } elseif ($m->tipo == 'salida') {
                    // Si sale antes de tiempo
                    if ($hora_str < $horario->hora_salida && !$tienePermiso) {
                        $m->estado_marcacion = 'Antes de Tiempo';
                    }
                    // Si sale más de 10 minutos después
                    elseif ($hora_marcacion > ($hora_salida + $tolerancia) && !$tienePermiso) {
                        $m->estado_marcacion = 'Tardanza en Salir';
                    }
                }
            }
        }
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
