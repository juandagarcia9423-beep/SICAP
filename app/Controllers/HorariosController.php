<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class HorariosController extends Controller {
    private $horarioModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->horarioModel = $this->model('Horario');
    }

    public function index() {
        if (!SesionHelper::tienePermiso('horarios', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }
        $this->view('horarios/index');
    }

    public function planta() {
        if (!SesionHelper::tienePermiso('horarios', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver esta configuración.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $data = [
            'titulo' => 'Horarios: Personal Planta',
            'tipo_personal' => 'planta',
            'turnos' => $this->horarioModel->obtenerTurnos(),
            'configuraciones' => $this->horarioModel->obtenerConfiguraciones('planta')
        ];
        $this->view('horarios/planta', $data);
    }

    public function administrativo() {
        if (!SesionHelper::tienePermiso('horarios', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver esta configuración.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $data = [
            'titulo' => 'Horarios: Personal Administrativo',
            'tipo_personal' => 'administrativo',
            'turnos' => $this->horarioModel->obtenerTurnos(),
            'configuraciones' => $this->horarioModel->obtenerConfiguraciones('administrativo')
        ];
        $this->view('horarios/administrativo', $data);
    }

    public function guardar() {
        if (!SesionHelper::tienePermiso('horarios', 'editar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para modificar horarios.";
            header('location: ' . URLROOT . '/horarios/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tipo = $_POST['tipo_personal'];
            foreach ($_POST['config'] as $dia => $turnos) {
                foreach ($turnos as $nombreTurno => $conf) {
                    $data = [
                        'tipo_personal' => $tipo,
                        'dia_semana' => $dia,
                        'turno_nombre' => $nombreTurno,
                        'hora_entrada' => $conf['hora_entrada'] ?? null,
                        'hora_salida' => $conf['hora_salida'] ?? null,
                        'horas_ordinarias' => $conf['horas'],
                        'activo' => isset($conf['activo']) ? 1 : 0
                    ];
                    $this->horarioModel->guardarConfiguracion($data);
                }
            }
            $_SESSION['mensaje_exito'] = "Configuración guardada.";
            header('location: ' . URLROOT . '/horarios/' . $tipo);
        }
    }

    public function asignar() {
        if (!SesionHelper::tienePermiso('horarios', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver esta sección.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $db = new \app\Core\Database();
        // Filtrar específicamente por área 'Planta'
        $db->query("SELECT id, nombre, area, turno_asignado FROM usuarios WHERE activo = 1 AND area = 'Planta'");
        $empleados = $db->resultSet();
        
        // DEBUG: Uncomment the following line to verify if data reaches the controller
        // die(var_dump($empleados));

        $data = [
            'titulo' => 'Asignar Turnos - Personal Planta',
            'empleados' => $empleados
        ];
        $this->view('horarios/asignar', $data);
    }

    public function guardarAsignacion() {
        if (!SesionHelper::tienePermiso('horarios', 'editar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para asignar horarios.";
            header('location: ' . URLROOT . '/horarios/asignar');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new \app\Core\Database();
            
            // Caso: Desasignar todos de un turno específico
            if (isset($_POST['desasignar_turno']) && !empty($_POST['turno'])) {
                $db->query("UPDATE usuarios SET turno_asignado = NULL WHERE turno_asignado = :turno");
                $db->bind(':turno', $_POST['turno']);
                $db->execute();
                $_SESSION['mensaje_exito'] = "Todos los empleados fueron desasignados del " . $_POST['turno'] . ".";
            } 
            // Caso: Asignación normal
            else {
                $usuario_ids = $_POST['empleado_ids'] ?? [];
                $turno = $_POST['empleados_turno'];
                
                if (!empty($usuario_ids) && !empty($turno)) {
                    $errors = [];
                    foreach ($usuario_ids as $usuario_id) {
                        // Verificar si ya tiene turno
                        $db->query("SELECT turno_asignado FROM usuarios WHERE id = :usuario_id");
                        $db->bind(':usuario_id', $usuario_id);
                        $user = $db->single();
                        
                        if ($user && !empty($user->turno_asignado)) {
                            $db->query("SELECT nombre FROM usuarios WHERE id = :usuario_id");
                            $db->bind(':usuario_id', $usuario_id);
                            $nombre = $db->single()->nombre;
                            $errors[] = "El empleado " . $nombre . " ya tiene asignado el " . $user->turno_asignado . ". Desasígnelo primero.";
                        } else {
                            $db->query("UPDATE usuarios SET turno_asignado = :turno WHERE id = :usuario_id");
                            $db->bind(':turno', $turno);
                            $db->bind(':usuario_id', $usuario_id);
                            $db->execute();
                        }
                    }
                    
                    if (!empty($errors)) {
                        $_SESSION['mensaje_error'] = implode("<br>", $errors);
                    } else {
                        $_SESSION['mensaje_exito'] = "Asignaciones guardadas correctamente.";
                    }
                } else {
                    $_SESSION['mensaje_error'] = "Seleccione al menos un empleado y un turno.";
                }
            }
            header('location: ' . URLROOT . '/horarios/asignar');
        }
    }
}
