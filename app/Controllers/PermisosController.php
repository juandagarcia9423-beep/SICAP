<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class PermisosController extends Controller {
    private $permisoModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->permisoModel = $this->model('Permiso');
    }

    public function index() {
        $usuario_id = $_SESSION['usuario_id'];
        
        $mis_solicitudes_raw = $this->permisoModel->obtenerMisSolicitudes($usuario_id);
        $autorizaciones_raw = $this->permisoModel->obtenerTodasParaAutorizar();

        $data = [
            'titulo' => 'Gestión de Permisos Laborales',
            'mis_solicitudes' => $this->agruparPorMes($mis_solicitudes_raw),
            'autorizaciones' => $this->agruparPorMes($autorizaciones_raw),
            'motivos' => $this->permisoModel->obtenerMotivos()
        ];

        $this->view('permisos/index', $data);
    }

    private function agruparPorMes($listado) {
        $agrupado = [];
        foreach ($listado as $item) {
            $mes = date('Y-m', strtotime($item->fecha_permiso));
            if (!isset($agrupado[$mes])) {
                $agrupado[$mes] = [];
            }
            $agrupado[$mes][] = $item;
        }
        return $agrupado;
    }

    public function solicitar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar envío
            $data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'motivo_id' => $_POST['motivo_id'],
                'fecha_permiso' => $_POST['fecha_permiso'],
                'hora_permiso' => $_POST['hora_permiso'],
                'horas_solicitadas' => $_POST['horas_solicitadas'],
                'regresa_laborar' => isset($_POST['regresa_laborar']) ? 1 : 0,
                'firma_digital' => $_POST['firma_digital'], // Base64 del canvas
                'requiere_reposicion' => $_POST['requiere_reposicion'],
                'reposicion_fecha' => $_POST['reposicion_fecha'] ?? null,
                'reposicion_hora' => $_POST['reposicion_hora'] ?? null,
                'reposicion_observacion' => $_POST['reposicion_observacion'] ?? '',
                'soporte_nombre' => ''
            ];

            // Manejo de PDF
            if (!empty($_FILES['soporte']['name'])) {
                $filename = time() . '_' . $_FILES['soporte']['name'];
                $upload_dir = APPROOT . '/public/uploads/permisos/';
                
                // Asegurar que el directorio existe
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['soporte']['tmp_name'], $upload_dir . $filename)) {
                    $data['soporte_nombre'] = 'uploads/permisos/' . $filename;
                }
            }

            if ($this->permisoModel->crearSolicitud($data)) {
                $_SESSION['mensaje_exito'] = "Solicitud de permiso enviada correctamente.";
                header('location: ' . URLROOT . '/permisos/index');
                exit();
            } else {
                die("Error al procesar solicitud");
            }

        } else {
            $data = [
                'titulo' => 'Nueva Solicitud de Permiso',
                'motivos' => $this->permisoModel->obtenerMotivos(),
                'fecha_minima' => date('Y-m-d', strtotime('+1 day'))
            ];
            $this->view('permisos/solicitar', $data);
        }
    }

    public function editar($id) {
        $solicitud = $this->permisoModel->obtenerPorId($id);

        // Verificar que la solicitud existe y pertenece al usuario, y que está en estado pendiente
        if (!$solicitud || $solicitud->usuario_id != $_SESSION['usuario_id'] || $solicitud->estado != 'pendiente') {
            header('location: ' . URLROOT . '/permisos/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'motivo_id' => $_POST['motivo_id'],
                'fecha_permiso' => $_POST['fecha_permiso'],
                'hora_permiso' => $_POST['hora_permiso'],
                'horas_solicitadas' => $_POST['horas_solicitadas'],
                'regresa_laborar' => isset($_POST['regresa_laborar']) ? 1 : 0,
                'firma_digital' => $_POST['firma_digital'],
                'requiere_reposicion' => $_POST['requiere_reposicion'],
                'reposicion_fecha' => $_POST['reposicion_fecha'] ?? null,
                'reposicion_hora' => $_POST['reposicion_hora'] ?? null,
                'reposicion_observacion' => $_POST['reposicion_observacion'] ?? '',
                'soporte_nombre' => $solicitud->soporte_nombre
            ];

            // Manejo de PDF
            if (!empty($_FILES['soporte']['name'])) {
                $filename = time() . '_' . $_FILES['soporte']['name'];
                $upload_dir = APPROOT . '/public/uploads/permisos/';
                
                // Asegurar que el directorio existe
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['soporte']['tmp_name'], $upload_dir . $filename)) {
                    $data['soporte_nombre'] = 'uploads/permisos/' . $filename;
                }
            }

            if ($this->permisoModel->actualizarSolicitud($data)) {
                $_SESSION['mensaje_exito'] = "Solicitud de permiso actualizada correctamente.";
                header('location: ' . URLROOT . '/permisos/index');
                exit();
            } else {
                die("Error al actualizar solicitud");
            }
        } else {
            $data = [
                'titulo' => 'Editar Solicitud de Permiso',
                'solicitud' => $solicitud,
                'motivos' => $this->permisoModel->obtenerMotivos()
            ];
            $this->view('permisos/editar', $data);
        }
    }

    public function aprobar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $firma = $_POST['firma_autorizacion'];
            $autorizador_id = $_SESSION['usuario_id'];
            
            if ($this->permisoModel->aprobarSolicitud($id, $firma, $autorizador_id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al aprobar']);
            }
        }
    }

    public function rechazar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $firma = $_POST['firma_rechazo'];
            $autorizador_id = $_SESSION['usuario_id'];
            
            if ($this->permisoModel->rechazarSolicitud($id, $firma, $autorizador_id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al rechazar']);
            }
        }
    }

    public function firmar_regreso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $firma = $_POST['firma_regreso_empleado'];
            
            if ($this->permisoModel->firmarRegresoEmpleado($id, $firma)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar firma de regreso']);
            }
        }
    }

    public function confirmar_regreso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $firma = $_POST['firma_regreso_autorizador'];
            
            if ($this->permisoModel->confirmarRegresoAutorizador($id, $firma)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al confirmar regreso']);
            }
        }
    }
}
