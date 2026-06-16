<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class InformesController extends Controller {
    private $asistenciaModel;
    private $usuarioModel;
    private $permisoModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->asistenciaModel = $this->model('Asistencia');
        $this->usuarioModel = $this->model('Usuario');
        $this->permisoModel = $this->model('Permiso');
    }

    public function index() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $data = [
            'titulo' => 'Módulo de Informes'
        ];

        $this->view('informes/index', $data);
    }

    public function asistencia() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? date('Y-m-01'),
            'fecha_fin' => $_GET['fecha_fin'] ?? date('Y-m-d'),
            'usuario_id' => $_GET['usuario_id'] ?? ''
        ];

        $marcaciones = $this->asistenciaModel->obtenerMarcaciones($filtros);
        $this->asistenciaModel->procesarEstadosMarcaciones($marcaciones);
        
        // Obtener usuarios para el filtro
        $db = new \app\Core\Database();
        $db->query("SELECT id, nombre, cedula FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
        $usuarios = $db->resultSet();

        $data = [
            'titulo' => 'Informe de Asistencia General',
            'marcaciones' => $marcaciones,
            'usuarios' => $usuarios,
            'filtros' => $filtros
        ];

        $this->view('informes/asistencia', $data);
    }

    public function excel_asistencia() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            exit("No tiene permiso");
        }

        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? date('Y-m-01'),
            'fecha_fin' => $_GET['fecha_fin'] ?? date('Y-m-d'),
            'usuario_id' => $_GET['usuario_id'] ?? ''
        ];

        // Obtener marcaciones ordenadas por usuario y fecha
        $db = new \app\Core\Database();
        $sql = "SELECT a.tipo, a.registrado_en, u.cedula, u.nombre 
                FROM asistencia a 
                JOIN usuarios u ON a.usuario_id = u.id 
                WHERE 1=1";
        
        $params = [];
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND a.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(a.registrado_en) >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(a.registrado_en) <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        $sql .= " ORDER BY u.nombre ASC, a.registrado_en ASC";

        $db->query($sql);
        foreach ($params as $key => $val) {
            $db->bind($key, $val);
        }
        $marcaciones = $db->resultSet();

        // Configurar cabeceras para descarga de Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Informe_Asistencia_Biometrico.xls');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #1e3a8a; color: white;">codigo biometrico</th>
                <th style="background-color: #1e3a8a; color: white;">ingreso</th>
                <th style="background-color: #1e3a8a; color: white;">TIPO</th>
              </tr>';

        foreach ($marcaciones as $m) {
            // Últimos 6 dígitos de la cédula
            $codigo_biometrico = substr($m->cedula, -6);
            
            // Fecha y hora en formato 12h
            $dt = new \DateTime($m->registrado_en);
            $ingreso = $dt->format('d/m/Y h:i:s A');
            
            // Tipo en mayúsculas
            $tipo = strtoupper($m->tipo);

            echo '<tr>';
            echo '<td style="vnd.ms-excel.numberformat:@">' . $codigo_biometrico . '</td>';
            echo '<td style="vnd.ms-excel.numberformat:@">' . $ingreso . '</td>';
            echo '<td>' . $tipo . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit();
    }

    public function permisos() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? date('Y-m-01'),
            'fecha_fin' => $_GET['fecha_fin'] ?? date('Y-m-d'),
            'usuario_id' => $_GET['usuario_id'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];

        $permisos = $this->permisoModel->obtenerSolicitudesParaInforme($filtros);
        
        // Obtener usuarios para el filtro
        $db = new \app\Core\Database();
        $db->query("SELECT id, nombre, cedula FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
        $usuarios = $db->resultSet();

        $data = [
            'titulo' => 'Informe de Permisos',
            'permisos' => $permisos,
            'usuarios' => $usuarios,
            'filtros' => $filtros
        ];

        $this->view('informes/permisos', $data);
    }

    public function excel_permisos() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            exit("No tiene permiso");
        }

        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? date('Y-m-01'),
            'fecha_fin' => $_GET['fecha_fin'] ?? date('Y-m-d'),
            'usuario_id' => $_GET['usuario_id'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];

        $permisos = $this->permisoModel->obtenerSolicitudesParaInforme($filtros);

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Informe_Permisos.xls');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #1e3a8a; color: white;">Empleado</th>
                <th style="background-color: #1e3a8a; color: white;">Cédula</th>
                <th style="background-color: #1e3a8a; color: white;">Motivo</th>
                <th style="background-color: #1e3a8a; color: white;">Fecha Permiso</th>
                <th style="background-color: #1e3a8a; color: white;">Hora</th>
                <th style="background-color: #1e3a8a; color: white;">Horas</th>
                <th style="background-color: #1e3a8a; color: white;">Estado</th>
                <th style="background-color: #1e3a8a; color: white;">Autorizado Por</th>
              </tr>';

        foreach ($permisos as $p) {
            echo '<tr>';
            echo '<td>' . $p->empleado_nombre . '</td>';
            echo '<td style="vnd.ms-excel.numberformat:@">' . $p->cedula . '</td>';
            echo '<td>' . $p->motivo_nombre . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($p->fecha_permiso)) . '</td>';
            echo '<td>' . date('h:i A', strtotime($p->hora_permiso)) . '</td>';
            echo '<td>' . $p->horas_solicitadas . '</td>';
            echo '<td>' . strtoupper($p->estado) . '</td>';
            echo '<td>' . ($p->autorizador_nombre ?? 'N/A') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit();
    }

    public function usuarios() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $db = new \app\Core\Database();
        $db->query("SELECT u.id, u.nombre, u.cedula, u.area, u.saldo_horas,
                    (SELECT COUNT(*) FROM asistencia WHERE usuario_id = u.id) as total_asistencias,
                    (SELECT COUNT(*) FROM solicitudes_permiso WHERE usuario_id = u.id AND estado = 'aprobada') as total_permisos,
                    (SELECT SUM(horas_solicitadas) FROM solicitudes_permiso WHERE usuario_id = u.id AND estado = 'aprobada') as horas_permisos
                    FROM usuarios u WHERE u.activo = 1 ORDER BY u.nombre ASC");
        $resumen = $db->resultSet();

        $data = [
            'titulo' => 'Resumen por Usuario',
            'resumen' => $resumen
        ];

        $this->view('informes/usuarios', $data);
    }

    public function excel_usuarios() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            exit("No tiene permiso");
        }

        $db = new \app\Core\Database();
        $db->query("SELECT u.id, u.nombre, u.cedula, u.area, u.saldo_horas,
                    (SELECT COUNT(*) FROM asistencia WHERE usuario_id = u.id) as total_asistencias,
                    (SELECT COUNT(*) FROM solicitudes_permiso WHERE usuario_id = u.id AND estado = 'aprobada') as total_permisos,
                    (SELECT SUM(horas_solicitadas) FROM solicitudes_permiso WHERE usuario_id = u.id AND estado = 'aprobada') as horas_permisos
                    FROM usuarios u WHERE u.activo = 1 ORDER BY u.nombre ASC");
        $resumen = $db->resultSet();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Resumen_Usuarios.xls');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #1e3a8a; color: white;">Nombre</th>
                <th style="background-color: #1e3a8a; color: white;">Cédula</th>
                <th style="background-color: #1e3a8a; color: white;">Área</th>
                <th style="background-color: #1e3a8a; color: white;">Marcaciones Totales</th>
                <th style="background-color: #1e3a8a; color: white;">Permisos Aprobados</th>
                <th style="background-color: #1e3a8a; color: white;">Horas de Permiso</th>
                <th style="background-color: #1e3a8a; color: white;">Saldo Banco Horas</th>
              </tr>';

        foreach ($resumen as $r) {
            echo '<tr>';
            echo '<td>' . $r->nombre . '</td>';
            echo '<td style="vnd.ms-excel.numberformat:@">' . $r->cedula . '</td>';
            echo '<td>' . $r->area . '</td>';
            echo '<td>' . $r->total_asistencias . '</td>';
            echo '<td>' . $r->total_permisos . '</td>';
            echo '<td>' . ($r->horas_permisos ?? 0) . '</td>';
            echo '<td>' . $r->saldo_horas . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit();
    }

    public function bancohoras() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $filtros = [
            'usuario_id' => $_GET['usuario_id'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? date('Y-m-01'),
            'fecha_fin' => $_GET['fecha_fin'] ?? date('Y-m-d')
        ];

        $db = new \app\Core\Database();
        $sql = "SELECT m.*, u.nombre as empleado_nombre, u.cedula, a.nombre as autorizador_nombre 
                FROM banco_horas_movimientos m 
                JOIN usuarios u ON m.usuario_id = u.id 
                LEFT JOIN usuarios a ON m.autorizado_por = a.id
                WHERE 1=1";
        
        $params = [];
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND m.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(m.fecha_movimiento) >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(m.fecha_movimiento) <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";
        $db->query($sql);
        foreach ($params as $key => $val) {
            $db->bind($key, $val);
        }
        $movimientos = $db->resultSet();

        $db->query("SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
        $usuarios = $db->resultSet();

        $data = [
            'titulo' => 'Informe de Banco de Horas',
            'movimientos' => $movimientos,
            'usuarios' => $usuarios,
            'filtros' => $filtros
        ];

        $this->view('informes/bancohoras', $data);
    }

    public function excel_bancohoras() {
        if (!SesionHelper::tienePermiso('informes', 'ver')) {
            exit("No tiene permiso");
        }

        $filtros = [
            'usuario_id' => $_GET['usuario_id'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? ''
        ];

        $db = new \app\Core\Database();
        $sql = "SELECT m.*, u.nombre as empleado_nombre, u.cedula, a.nombre as autorizador_nombre 
                FROM banco_horas_movimientos m 
                JOIN usuarios u ON m.usuario_id = u.id 
                LEFT JOIN usuarios a ON m.autorizado_por = a.id
                WHERE 1=1";
        
        $params = [];
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND m.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(m.fecha_movimiento) >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(m.fecha_movimiento) <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";
        $db->query($sql);
        foreach ($params as $key => $val) {
            $db->bind($key, $val);
        }
        $movimientos = $db->resultSet();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Informe_Banco_Horas.xls');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #1e3a8a; color: white;">Empleado</th>
                <th style="background-color: #1e3a8a; color: white;">Cédula</th>
                <th style="background-color: #1e3a8a; color: white;">Fecha</th>
                <th style="background-color: #1e3a8a; color: white;">Tipo</th>
                <th style="background-color: #1e3a8a; color: white;">Horas</th>
                <th style="background-color: #1e3a8a; color: white;">Concepto</th>
                <th style="background-color: #1e3a8a; color: white;">Autorizado Por</th>
              </tr>';

        foreach ($movimientos as $m) {
            echo '<tr>';
            echo '<td>' . $m->empleado_nombre . '</td>';
            echo '<td style="vnd.ms-excel.numberformat:@">' . $m->cedula . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($m->fecha_movimiento)) . '</td>';
            echo '<td>' . strtoupper($m->tipo) . '</td>';
            echo '<td>' . $m->horas . '</td>';
            echo '<td>' . $m->concepto . '</td>';
            echo '<td>' . ($m->autorizador_nombre ?? 'N/A') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit();
    }
}
