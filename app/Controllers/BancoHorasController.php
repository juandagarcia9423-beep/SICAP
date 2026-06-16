<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class BancoHorasController extends Controller {
    private $bancoModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->bancoModel = $this->model('BancoHoras');
    }

    public function index() {
        if (!SesionHelper::tienePermiso('bancohoras', 'ver')) {
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $usuario_id = $_GET['usuario_id'] ?? null;
        $filtro = $_GET['filtro'] ?? null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Obtener lista de usuarios para el filtro
        $usuarioModel = $this->model('Usuario');
        $usuarios = $usuarioModel->obtenerUsuarios();

        // Lógica de filtro especial para deudores
        $soloDeudores = ($filtro === 'deudores');
        
        $totalMovimientos = $this->bancoModel->contarMovimientos($usuario_id, $soloDeudores);
        $totalPages = ceil($totalMovimientos / $limit);

        $data = [
            'titulo' => $soloDeudores ? 'Empleados con Deuda de Tiempo' : 'Movimientos de Banco de Horas',
            'movimientos' => $this->bancoModel->obtenerMovimientos($usuario_id, $limit, $offset, $soloDeudores),
            'usuarios' => $usuarios,
            'usuario_id' => $usuario_id,
            'filtro' => $filtro,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
        $this->view('bancohoras/index', $data);
    }

    public function aprobar_desde_alerta() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && SesionHelper::tienePermiso('horarios', 'editar')) {
            $alerta_id = $_POST['alerta_id'];
            $usuario_id = $_POST['usuario_id'];
            $tipo_movimiento = $_POST['tipo_movimiento'] ?? 'credito';
            $horas_input = isset($_POST['horas']) && $_POST['horas'] !== '' ? (int)$_POST['horas'] : 0;
            $minutos_input = isset($_POST['minutos']) && $_POST['minutos'] !== '' ? (int)$_POST['minutos'] : 0;
            $concepto = $_POST['concepto'];
            $autorizador_id = $_SESSION['usuario_id'];

            // Convertir a decimal (ej. 1 hora 30 min = 1.5)
            $horas_totales = $horas_input + ($minutos_input / 60);

            if ($horas_totales <= 0) {
                $_SESSION['mensaje_error'] = "Debe especificar un tiempo mayor a 0.";
                header('location: ' . URLROOT . '/alertas/detalle?tipo=Tardanza en Salir');
                exit();
            }

            if ($this->bancoModel->registrarMovimiento([
                'usuario_id' => $usuario_id,
                'tipo' => $tipo_movimiento,
                'horas' => $horas_totales,
                'concepto' => $concepto,
                'autorizado_por' => $autorizador_id
            ])) {
                // Marcar alerta como leída
                $alertaModel = $this->model('Alerta');
                $alertaModel->toggleLeida($alerta_id);
                
                $_SESSION['mensaje_exito'] = "Movimiento registrado y aplicado al banco de horas.";
            } else {
                $_SESSION['mensaje_error'] = "Error al registrar movimiento.";
            }
            header('location: ' . URLROOT . '/alertas/detalle?tipo=Tardanza en Salir');
        } else {
            header('location: ' . URLROOT . '/dashboard');
        }
    }
}
