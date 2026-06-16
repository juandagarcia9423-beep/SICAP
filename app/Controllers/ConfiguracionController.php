<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class ConfiguracionController extends Controller {
    public function __construct() {
        SesionHelper::protegerRuta();
    }

    public function index() {
        if (!SesionHelper::tienePermiso('configuracion', 'ver')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para ver este módulo.";
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }
        $data = ['titulo' => 'Configuración del Sistema'];
        $this->view('configuracion/index', $data);
    }

    public function metodosAuth() {
        if (!SesionHelper::tienePermiso('configuracion', 'metodos_acceso')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para configurar métodos de acceso.";
            header('location: ' . URLROOT . '/configuracion/index');
            exit();
        }

        $db = new \app\Core\Database();
        $db->query("SELECT * FROM configuracion_seguridad");
        $configs = $db->resultSet();
        $configData = [];
        foreach($configs as $c) $configData[$c->clave] = $c->valor;

        $this->view('configuracion/metodos_auth', [
            'titulo' => 'Métodos de Autenticación',
            'config' => $configData
        ]);
    }

    public function guardarMetodosAuth() {
        if (!SesionHelper::tienePermiso('configuracion', 'metodos_acceso')) {
            $_SESSION['mensaje_error'] = "Acceso denegado.";
            header('location: ' . URLROOT . '/configuracion/metodosAuth');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new \app\Core\Database();
            $keys = ['pin', 'facial', 'qr'];
            foreach ($keys as $key) {
                $val = isset($_POST[$key]) ? 1 : 0;
                $db->query("UPDATE configuracion_seguridad SET valor = :valor WHERE clave = :key");
                $db->bind(':valor', $val);
                $db->bind(':key', $key);
                $db->execute();
            }
            $_SESSION['mensaje_exito'] = "Configuración actualizada.";
            header('location: ' . URLROOT . '/configuracion/metodosAuth');
        }
    }

    public function seguridad() {
        if (!SesionHelper::tienePermiso('configuracion', 'seguridad')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para gestionar seguridad.";
            header('location: ' . URLROOT . '/configuracion/index');
            exit();
        }

        $db = new \app\Core\Database();
        $db->query("SELECT DISTINCT area FROM usuarios WHERE activo = 1 AND area != '' AND area IS NOT NULL");
        $areas = $db->resultSet();
        
        // Fetch Auth Config
        $db->query("SELECT * FROM configuracion_seguridad");
        $authSettings = $db->resultSet();
        $authConfig = [];
        foreach($authSettings as $as) $authConfig[$as->clave] = $as->valor;

        // Obtener permisos del usuario actual para la delegación jerárquica
        $misPermisosRaw = $this->getPermisosUsuarioActual();
        $misPermisos = [];
        foreach($misPermisosRaw as $mp) {
            $misPermisos[$mp->modulo][] = $mp->accion;
        }

        $data = [
            'titulo' => 'Seguridad y Permisos por Usuario',
            'usuarios' => $this->getUsuariosLista(),
            'modulos' => ['dashboard', 'usuarios', 'asistencia', 'permisos', 'horarios', 'alertas', 'informes', 'configuracion'],
            'acciones' => ['ver', 'crear', 'editar', 'eliminar', 'configurar', 'ver_estadisticas'],
            'areas_todas' => $areas,
            'authConfig' => $authConfig,
            'misPermisos' => $misPermisos // Pasamos nuestros propios permisos a la vista
        ];
        $this->view('configuracion/seguridad', $data);
    }

    private function getPermisosUsuarioActual() {
        $db = new \app\Core\Database();
        $db->query("SELECT p.modulo, p.accion 
                    FROM usuarios_permisos up 
                    JOIN permisos p ON up.permiso_id = p.id 
                    WHERE up.usuario_id = :id");
        $db->bind(':id', $_SESSION['usuario_id']);
        return $db->resultSet();
    }

    public function obtenerConfigAutorizacion($id) {
        if (!SesionHelper::tienePermiso('configuracion', 'seguridad')) {
            echo json_encode([]);
            exit();
        }
        $db = new \app\Core\Database();
        $db->query("SELECT * FROM configuracion_autorizadores WHERE autorizador_id = :id");
        $db->bind(':id', $id);
        $config = $db->single();
        echo json_encode($config ?: (object)[]);
    }

    public function guardarConfigAutorizacion() {
        if (!SesionHelper::tienePermiso('configuracion', 'seguridad')) {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $autorizador_id = $_POST['usuario_id'];
            
            $areas_input = isset($_POST['areas']) ? $_POST['areas'] : '[]';
            $areas_array = json_decode($areas_input, true);
            $areas = is_array($areas_array) ? json_encode($areas_array) : '[]';

            $users_input = isset($_POST['usuarios']) ? $_POST['usuarios'] : '[]';
            $users_array = json_decode($users_input, true);
            $usuarios = is_array($users_array) ? json_encode($users_array) : '[]';

            $db = new \app\Core\Database();
            $db->query("INSERT INTO configuracion_autorizadores (autorizador_id, areas_permitidas, usuarios_permitidos) 
                        VALUES (:uid, :areas, :users)
                        ON DUPLICATE KEY UPDATE areas_permitidas = :areas, usuarios_permitidos = :users");
            $db->bind(':uid', $autorizador_id);
            $db->bind(':areas', $areas);
            $db->bind(':users', $usuarios);
            
            if ($db->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }

    public function motivos() {
        if (!SesionHelper::tienePermiso('configuracion', 'motivos_permiso')) {
            $_SESSION['mensaje_error'] = "No tiene permiso para gestionar motivos de permisos.";
            header('location: ' . URLROOT . '/configuracion/index');
            exit();
        }

        $permisoModel = $this->model('Permiso');
        
        $db = new \app\Core\Database();
        $db->query("SELECT DISTINCT area FROM usuarios WHERE activo = 1 AND area != '' AND area IS NOT NULL");
        $areas = $db->resultSet();
        
        $db->query("SELECT id, nombre, cedula FROM usuarios WHERE activo = 1");
        $usuarios = $db->resultSet();

        $data = [
            'titulo' => 'Configuración de Motivos de Permisos',
            'motivos' => $permisoModel->obtenerMotivos(),
            'areas' => $areas,
            'usuarios' => $usuarios
        ];
        $this->view('configuracion/motivos', $data);
    }

    public function guardarMotivo() {
        if (!SesionHelper::tienePermiso('configuracion', 'configurar')) {
            $_SESSION['mensaje_error'] = "Acceso denegado.";
            header('location: ' . URLROOT . '/configuracion/motivos');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            
            $areas = isset($_POST['areas_permitidas']) && is_array($_POST['areas_permitidas']) ? json_encode($_POST['areas_permitidas']) : null;
            $usuarios = isset($_POST['usuarios_permitidos']) && is_array($_POST['usuarios_permitidos']) ? json_encode($_POST['usuarios_permitidos']) : null;
            
            $data = [
                'id' => $id,
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion']),
                'repone_tiempo' => isset($_POST['repone_tiempo']) ? 1 : 0,
                'visible_para_usuarios' => isset($_POST['visible_para_usuarios']) ? 1 : 0,
                'areas_permitidas' => $areas,
                'usuarios_permitidos' => $usuarios
            ];

            $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            if ($id) {
                $stmt = $conn->prepare("UPDATE motivos_permiso SET nombre = :nombre, descripcion = :descripcion, repone_tiempo = :repone_tiempo, visible_para_usuarios = :visible_para_usuarios, areas_permitidas = :areas_permitidas, usuarios_permitidos = :usuarios_permitidos WHERE id = :id");
                $stmt->execute($data);
                $_SESSION['mensaje_exito'] = "Motivo actualizado correctamente.";
            } else {
                $stmt = $conn->prepare("INSERT INTO motivos_permiso (nombre, descripcion, repone_tiempo, visible_para_usuarios, areas_permitidas, usuarios_permitidos) VALUES (:nombre, :descripcion, :repone_tiempo, :visible_para_usuarios, :areas_permitidas, :usuarios_permitidos)");
                unset($data['id']);
                $stmt->execute($data);
                $_SESSION['mensaje_exito'] = "Nuevo motivo de permiso creado.";
            }
            header('location: ' . URLROOT . '/configuracion/motivos');
        }
    }

    public function eliminarMotivo($id) {
        if (!SesionHelper::tienePermiso('configuracion', 'configurar')) {
            $_SESSION['mensaje_error'] = "Acceso denegado.";
            header('location: ' . URLROOT . '/configuracion/motivos');
            exit();
        }
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
        if (!SesionHelper::tienePermiso('configuracion', 'seguridad')) {
            echo json_encode([]);
            exit();
        }
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
        if (!SesionHelper::tienePermiso('configuracion', 'seguridad')) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
            exit();
        }
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
