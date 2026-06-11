<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class ConfiguracionController extends Controller {
    public function __construct() {
        SesionHelper::protegerRuta();
    }

    public function index() {
        $data = ['titulo' => 'Configuración del Sistema'];
        $this->view('configuracion/index', $data);
    }

    public function seguridad() {
        $data = [
            'titulo' => 'Seguridad y Permisos por Usuario',
            'usuarios' => $this->getUsuariosLista(),
            'modulos' => ['dashboard', 'usuarios', 'asistencia', 'permisos', 'horarios', 'alertas', 'informes'],
            'acciones' => ['ver', 'crear', 'editar', 'eliminar', 'configurar', 'ver_estadisticas']
        ];
        $this->view('configuracion/seguridad', $data);
    }

    public function motivos() {
        $permisoModel = $this->model('Permiso');
        $data = [
            'titulo' => 'Configuración de Motivos de Permisos',
            'motivos' => $permisoModel->obtenerMotivos()
        ];
        $this->view('configuracion/motivos', $data);
    }

    public function guardarMotivo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'id' => $id,
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion']),
                'repone_tiempo' => isset($_POST['repone_tiempo']) ? 1 : 0,
                'visible_para_usuarios' => isset($_POST['visible_para_usuarios']) ? 1 : 0
            ];

            $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            if ($id) {
                $stmt = $conn->prepare("UPDATE motivos_permiso SET nombre = :nombre, descripcion = :descripcion, repone_tiempo = :repone_tiempo, visible_para_usuarios = :visible_para_usuarios WHERE id = :id");
                $stmt->execute($data);
                $_SESSION['mensaje_exito'] = "Motivo actualizado correctamente.";
            } else {
                $stmt = $conn->prepare("INSERT INTO motivos_permiso (nombre, descripcion, repone_tiempo, visible_para_usuarios) VALUES (:nombre, :descripcion, :repone_tiempo, :visible_para_usuarios)");
                unset($data['id']);
                $stmt->execute($data);
                $_SESSION['mensaje_exito'] = "Nuevo motivo de permiso creado.";
            }
            header('location: ' . URLROOT . '/configuracion/motivos');
        }
    }

    public function eliminarMotivo($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $stmt = $conn->prepare("DELETE FROM motivos_permiso WHERE id = :id");
            if ($stmt->execute([':id' => $id])) {
                $_SESSION['mensaje_exito'] = "Motivo eliminado correctamente.";
            }
            header('location: ' . URLROOT . '/configuracion/motivos');
        }
    }

    public function obtenerPermisosUsuario($id) {
        $db = new \app\Core\Database();
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $stmt = $conn->prepare("SELECT p.modulo, p.accion FROM usuarios_permisos up 
                                JOIN permisos p ON up.permiso_id = p.id 
                                WHERE up.usuario_id = :id");
        $stmt->execute([':id' => $id]);
        $permisos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($permisos);
    }

    public function guardarPermisosUsuario() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'];
            $permisos_nuevos = json_decode($_POST['permisos'], true);

            $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $conn->beginTransaction();

            try {
                // 1. Eliminar permisos actuales
                $stmtDel = $conn->prepare("DELETE FROM usuarios_permisos WHERE usuario_id = :id");
                $stmtDel->execute([':id' => $usuario_id]);

                // 2. Insertar nuevos permisos
                foreach ($permisos_nuevos as $p) {
                    $stmtGetId = $conn->prepare("SELECT id FROM permisos WHERE modulo = :m AND accion = :a");
                    $stmtGetId->execute([':m' => $p['modulo'], ':a' => $p['accion']]);
                    $permiso_id = $stmtGetId->fetchColumn();

                    if ($permiso_id) {
                        $stmtIns = $conn->prepare("INSERT INTO usuarios_permisos (usuario_id, permiso_id) VALUES (:u, :p)");
                        $stmtIns->execute([':u' => $usuario_id, ':p' => $permiso_id]);
                    }
                }

                $conn->commit();
                echo json_encode(['status' => 'success']);
            } catch (\Exception $e) {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    private function getUsuariosLista() {
        return $this->getUsersFromDB();
    }

    private function getUsersFromDB() {
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $stmt = $conn->query("SELECT id, nombre, usuario FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
