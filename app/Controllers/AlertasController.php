<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class AlertasController extends Controller {
    private $alertaModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->alertaModel = $this->model('Alerta');
    }

    public function index() {
        if (!SesionHelper::tienePermiso('alertas', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $data = [
            'titulo' => 'Gestión de Alertas',
            'contadores' => $this->alertaModel->obtenerContadorAlertas()
        ];
        $this->view('alertas/index', $data);
    }

    public function detalle() {
        if (!SesionHelper::tienePermiso('alertas', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver el detalle de alertas.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $filtros = ['tipo' => $_GET['tipo'] ?? ''];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $totalAlertas = $this->alertaModel->contarAlertas($filtros);
        $totalPages = ceil($totalAlertas / $limit);
        
        $titulo = !empty($filtros['tipo']) ? 'Alertas: ' . $filtros['tipo'] : 'Todas las Alertas';
        $data = [
            'titulo' => $titulo,
            'alertas' => $this->alertaModel->obtenerAlertas($filtros, $limit, $offset),
            'filtros' => $filtros,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
        $this->view('alertas/detalle', $data);
    }

    public function toggle($id) {
        if (!SesionHelper::tienePermiso('alertas', 'editar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para editar el estado de las alertas.";
            header('location: ' . URLROOT . '/alertas/detalle?tipo=' . urlencode($_GET['tipo'] ?? ''));
            exit();
        }

        $this->alertaModel->toggleLeida($id);
        $_SESSION['mensaje_exito'] = "Estado de alerta actualizado.";
        header('location: ' . URLROOT . '/alertas/detalle?tipo=' . urlencode($_GET['tipo'] ?? ''));
    }

    public function eliminar($id) {
        if (!SesionHelper::tienePermiso('alertas', 'eliminar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para eliminar alertas.";
            header('location: ' . URLROOT . '/alertas/detalle?tipo=' . urlencode($_GET['tipo'] ?? ''));
            exit();
        }

        $this->alertaModel->eliminarAlerta($id);
        $_SESSION['mensaje_exito'] = "Alerta eliminada.";
        
        // Redirigir de vuelta con los mismos filtros
        $tipo = $_GET['tipo'] ?? '';
        $page = $_GET['page'] ?? 1;
        header('location: ' . URLROOT . "/alertas/detalle?page=$page&tipo=" . urlencode($tipo));
    }

    public function eliminar_masivo() {
        if (!SesionHelper::tienePermiso('alertas', 'eliminar')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para realizar eliminaciones masivas.";
            header('location: ' . URLROOT . '/alertas/detalle?tipo=' . urlencode($_POST['tipo_filtro'] ?? ''));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alertas_ids'])) {
            $ids = explode(',', $_POST['alertas_ids']);
            $count = 0;
            foreach ($ids as $id) {
                $id = trim($id);
                if (!empty($id) && is_numeric($id)) {
                    if ($this->alertaModel->eliminarAlerta($id)) {
                        $count++;
                    }
                }
            }
            if ($count > 0) {
                $_SESSION['mensaje_exito'] = "$count alertas eliminadas correctamente.";
            } else {
                $_SESSION['mensaje_error'] = "No se pudieron eliminar las alertas seleccionadas.";
            }
        }
        
        $tipo = $_POST['tipo_filtro'] ?? '';
        $page = $_POST['page_actual'] ?? 1;
        header('location: ' . URLROOT . "/alertas/detalle?page=$page&tipo=" . urlencode($tipo));
    }
}
