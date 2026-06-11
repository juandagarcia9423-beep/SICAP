<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class DashboardController extends Controller {
    public function __construct() {
        // Proteger la ruta para que solo entren logueados
        SesionHelper::protegerRuta();
    }

    public function index() {
        $db = new \app\Core\Database();
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        
        $usuario_id = $_SESSION['usuario_id'];
        
        // Verificar permiso para ver estadísticas
        $stmtPermiso = $conn->prepare("SELECT COUNT(*) FROM usuarios_permisos up 
                                      JOIN permisos p ON up.permiso_id = p.id 
                                      WHERE up.usuario_id = :usuario_id 
                                      AND p.modulo = 'dashboard' AND p.accion = 'ver_estadisticas'");
        $stmtPermiso->execute([':usuario_id' => $usuario_id]);
        $puedeVerStats = $stmtPermiso->fetchColumn() > 0;

        // Si es superadmin o tiene el permiso, mostrar datos reales, si no, stats vacíos
        if ($puedeVerStats || $_SESSION['usuario_rol'] == 'superadmin') {
            // 1. Total Usuarios
            $totalUsuarios = $conn->query("SELECT COUNT(*) FROM usuarios WHERE activo = 1")->fetchColumn();
            
            // 2. Asistencia Hoy
            $hoy = date('Y-m-d');
            $asistenciaHoy = $conn->query("SELECT COUNT(DISTINCT usuario_id) FROM asistencia WHERE tipo = 'entrada' AND DATE(registrado_en) = '$hoy'")->fetchColumn();
            $porcentajeAsistencia = ($totalUsuarios > 0) ? round(($asistenciaHoy / $totalUsuarios) * 100) : 0;

            $stats = [
                'total_usuarios' => $totalUsuarios,
                'asistencia_hoy' => $porcentajeAsistencia . '%',
                'permisos_pendientes' => 0,
                'alertas_activas' => 0
            ];
        } else {
            $stats = null; // Indica que no se deben mostrar
        }

        $data = [
            'titulo' => 'Dashboard Principal',
            'stats' => $stats,
            'puede_ver_stats' => ($stats !== null)
        ];
        $this->view('dashboard/index', $data);
    }
}
