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

            // 3. Permisos Pendientes
            $permisosPendientes = $conn->query("SELECT COUNT(*) FROM solicitudes_permiso WHERE estado = 'pendiente'")->fetchColumn();

            // 4. Alertas Activas (No leídas)
            $alertasActivas = $conn->query("SELECT COUNT(*) FROM alertas WHERE leido = FALSE")->fetchColumn();

            // 5. Total Deuda Banco Horas (Global)
            $totalDeuda = $conn->query("SELECT SUM(saldo_horas) FROM usuarios WHERE saldo_horas < 0")->fetchColumn();

            $stats = [
                'total_usuarios' => $totalUsuarios,
                'asistencia_hoy' => $porcentajeAsistencia . '%',
                'permisos_pendientes' => $permisosPendientes,
                'alertas_activas' => $alertasActivas,
                'total_deuda_horas' => abs($totalDeuda ?: 0)
            ];
        } else {
            $stats = null; // Indica que no se deben mostrar
        }

        // Estadísticas para empleados
        $bancoModel = $this->model('BancoHoras');
        $saldo = $bancoModel->obtenerSaldo($_SESSION['usuario_id']);

        // --- Lógica de Alerta de Contraseña ---
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerUsuarioPorId($_SESSION['usuario_id']);
        
        $alertaPassword = false;
        $diasConfigurados = $usuario->dias_cambio_password;

        if ($usuario->alerta_cambio_password == 1) {
            $fechaUltimoCambio = new \DateTime(date('Y-m-d', strtotime($usuario->ultimo_cambio_password)));
            $hoy = new \DateTime(date('Y-m-d'));
            $diferencia = $hoy->diff($fechaUltimoCambio)->days;

            if ($diferencia >= $diasConfigurados) {
                $alertaPassword = true;
            }
        }
        // --------------------------------------

        $data = [
            'titulo' => 'Dashboard Principal',
            'stats' => $stats,
            'saldo' => $saldo,
            'puede_ver_stats' => ($stats !== null),
            'alerta_password' => $alertaPassword,
            'dias_configurados' => $diasConfigurados
        ];
        $this->view('dashboard/index', $data);
    }
}
