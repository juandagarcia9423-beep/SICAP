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
        $autorizaciones_raw = $this->permisoModel->obtenerTodasParaAutorizar($usuario_id);

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
            $horas_input = isset($_POST['horas']) && $_POST['horas'] !== '' ? (int)$_POST['horas'] : 0;
            $minutos_input = isset($_POST['minutos']) && $_POST['minutos'] !== '' ? (int)$_POST['minutos'] : 0;
            $horas_totales = $horas_input + ($minutos_input / 60);

            // Obtener configuración del motivo para validar forma de pago
            $motive_config = $this->permisoModel->obtenerMotivoPorId($_POST['motivo_id']);
            $permite_pago = isset($motive_config->permite_forma_pago) ? $motive_config->permite_forma_pago : 0;
            
            // Si el motivo NO permite elegir forma de pago, forzamos a 'remunerado' 
            // (para que no se use basura de posts anteriores o valores por defecto erróneos)
            $forma_pago = ($permite_pago == 1) ? $_POST['forma_pago'] : 'remunerado';

            // Procesar envío
            $data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'motivo_id' => $_POST['motivo_id'],
                'fecha_permiso' => $_POST['fecha_permiso'],
                'hora_permiso' => $_POST['hora_permiso'],
                'horas_solicitadas' => $horas_totales,
                'forma_pago' => $forma_pago,
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
                'motivos' => $this->permisoModel->obtenerMotivosParaUsuario($_SESSION['usuario_id']),
                'fecha_minima' => date('Y-m-d', strtotime('+1 day'))
            ];
            $this->view('permisos/solicitar', $data);
        }
    }

    public function editar($id) {
        $solicitud = $this->permisoModel->obtenerPorId($id);

        // Verificar que la solicitud existe y está en estado pendiente
        if (!$solicitud || $solicitud->estado != 'pendiente') {
            header('location: ' . URLROOT . '/permisos/index');
            exit();
        }

        // Obtener configuración del motivo seleccionado
        $motive_config = $this->permisoModel->obtenerMotivoPorId($solicitud->motivo_id);

        // Permitir editar si es el dueño O si tiene permiso de edición (autorizador/admin)
        $esDuenio = ($solicitud->usuario_id == $_SESSION['usuario_id']);
        $esAutorizador = SesionHelper::tienePermiso('permisos', 'editar');

        if (!$esDuenio && !$esAutorizador) {
            $_SESSION['mensaje_error'] = "No tiene permiso para editar esta solicitud.";
            header('location: ' . URLROOT . '/permisos/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $horas_input = isset($_POST['horas']) ? (int)$_POST['horas'] : 0;
            $minutos_input = isset($_POST['minutos']) ? (int)$_POST['minutos'] : 0;
            $horas_totales = $horas_input + ($minutos_input / 60);

            $data = [
                'id' => $id,
                'motivo_id' => $_POST['motivo_id'],
                'fecha_permiso' => $_POST['fecha_permiso'],
                'hora_permiso' => $_POST['hora_permiso'],
                'horas_solicitadas' => $horas_totales,
                'forma_pago' => $_POST['forma_pago'] ?? 'remunerado', // Default remunerado if hidden
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
                'motivos' => $this->permisoModel->obtenerMotivosParaUsuario($_SESSION['usuario_id']),
                'motive_config' => $motive_config
            ];
            $this->view('permisos/editar', $data);
        }
    }

    public function aprobar() {
        header('Content-Type: application/json');
        if (!SesionHelper::tienePermiso('permisos', 'editar')) {
            echo json_encode(['success' => false, 'message' => 'No tiene permiso para autorizar']);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];
                $firma = $_POST['firma_autorizacion'];
                $autorizador_id = $_SESSION['usuario_id'];
                
                if ($this->permisoModel->aprobarSolicitud($id, $firma, $autorizador_id)) {
                    // Cargar detalles para banco de horas y alertas
                    $permiso = $this->permisoModel->obtenerDetalleCompleto($id);
                    
                    if ($permiso) {
                        // 1. Manejar Banco de Horas
                        // Determinar si el motivo es para acreditar o debitar horas
                        $motive_config = $this->permisoModel->obtenerMotivoPorId($permiso->motivo_id);
                        $es_credito = isset($motive_config->es_credito) ? $motive_config->es_credito : 0;
                        
                        if ($es_credito == 1) {
                            $bancoModel = $this->model('BancoHoras');
                            $bancoModel->registrarMovimiento([
                                'usuario_id' => $permiso->usuario_id,
                                'tipo' => 'credito',
                                'horas' => $permiso->horas_solicitadas,
                                'concepto' => "Abono por: " . $permiso->motivo_nombre,
                                'autorizado_por' => $autorizador_id
                            ]);
                        } else if ($permiso->forma_pago == 'banco_horas' || $permiso->forma_pago == 'reposicion') {
                            $bancoModel = $this->model('BancoHoras');
                            $concepto = ($permiso->forma_pago == 'banco_horas') ? 'Uso de Banco de Horas' : 'Permiso por reponer';
                            $bancoModel->registrarMovimiento([
                                'usuario_id' => $permiso->usuario_id,
                                'tipo' => 'debito',
                                'horas' => $permiso->horas_solicitadas,
                                'concepto' => $concepto . ": " . $permiso->motivo_nombre,
                                'autorizado_por' => $autorizador_id
                            ]);
                        }

                        // 2. Generar Alerta Inmediata
                        $alertaModel = $this->model('Alerta');
                        $hora_fmt = date('h:i A', strtotime($permiso->hora_permiso));
                        $fecha_fmt = date('d/m/Y', strtotime($permiso->fecha_permiso));
                        $descripcion = "El empleado {$permiso->empleado_nombre} tiene un permiso de {$permiso->motivo_nombre} aprobado para el día {$fecha_fmt} a las {$hora_fmt} por {$permiso->horas_solicitadas} horas.";
                        $identificador = "permiso_id_" . $permiso->id;

                        $alertaModel->registrarAlerta(
                            $permiso->usuario_id, 
                            'Permiso Laboral', 
                            $descripcion,
                            $identificador
                        );
                    }
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al aprobar']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function rechazar() {
        header('Content-Type: application/json');
        if (!SesionHelper::tienePermiso('permisos', 'editar')) {
            echo json_encode(['success' => false, 'message' => 'No tiene permiso para rechazar']);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];
                $firma = $_POST['firma_rechazo'];
                $autorizador_id = $_SESSION['usuario_id'];
                
                if ($this->permisoModel->rechazarSolicitud($id, $firma, $autorizador_id)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al rechazar']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function firmar_regreso() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];
                $firma = $_POST['firma_regreso_empleado'];
                
                if ($this->permisoModel->firmarRegresoEmpleado($id, $firma)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al guardar firma de regreso']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function confirmar_regreso() {
        header('Content-Type: application/json');
        if (!SesionHelper::tienePermiso('permisos', 'editar')) {
            echo json_encode(['success' => false, 'message' => 'No tiene permiso para confirmar regreso']);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];
                $firma = $_POST['firma_regreso_autorizador'];
                
                if ($this->permisoModel->confirmarRegresoAutorizador($id, $firma)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al confirmar regreso']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
}
