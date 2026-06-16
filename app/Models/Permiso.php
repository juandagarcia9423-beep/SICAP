<?php
namespace app\Models;

use app\Core\Database;

class Permiso {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Obtener solicitudes de un usuario específico
    public function obtenerMisSolicitudes($usuario_id) {
        $this->db->query("SELECT s.*, m.nombre as motivo_nombre, u.cedula, u.area, a.nombre as autorizador_nombre 
                          FROM solicitudes_permiso s 
                          JOIN motivos_permiso m ON s.motivo_id = m.id 
                          JOIN usuarios u ON s.usuario_id = u.id
                          LEFT JOIN usuarios a ON s.autorizado_por = a.id
                          WHERE s.usuario_id = :usuario_id 
                          ORDER BY s.creado_en DESC");
        $this->db->bind(':usuario_id', $usuario_id);
        return $this->db->resultSet();
    }

    // Obtener todas las solicitudes para autorizar (con filtros de seguridad)
    public function obtenerTodasParaAutorizar($autorizador_id) {
        // 1. Obtener la configuración de este autorizador
        $this->db->query("SELECT * FROM configuracion_autorizadores WHERE autorizador_id = :id");
        $this->db->bind(':id', $autorizador_id);
        $config = $this->db->single();

        // 2. Si no tiene configuración, verificar si existen otras configuraciones
        if (!$config) {
            $this->db->query("SELECT COUNT(*) as total FROM configuracion_autorizadores");
            $total_configs = $this->db->single()->total;
            if ($total_configs > 0) return [];
        }

        // 3. Construir la consulta con filtros
        $sql = "SELECT s.*, m.nombre as motivo_nombre, u.nombre as empleado_nombre, u.cedula, u.area, a.nombre as autorizador_nombre 
                FROM solicitudes_permiso s 
                JOIN motivos_permiso m ON s.motivo_id = m.id 
                JOIN usuarios u ON s.usuario_id = u.id 
                LEFT JOIN usuarios a ON s.autorizado_por = a.id
                WHERE s.usuario_id != :autorizador_id";
        
        $named_params = [':autorizador_id' => $autorizador_id];

        if ($config) {
            $areas = $config->areas_permitidas ? json_decode($config->areas_permitidas, true) : [];
            $usuarios = $config->usuarios_permitidos ? json_decode($config->usuarios_permitidos, true) : [];

            // Si se selecciona "TODAS LAS ÁREAS" o "TODOS LOS USUARIOS", no filtramos más (acceso total pero sin sí mismo)
            if (in_array('*', $areas) || in_array('*', $usuarios)) {
                // No añadimos nada a los filtros
            } else {
                $filtros = [];
                if (!empty($areas)) {
                    $area_placeholders = [];
                    foreach ($areas as $i => $area) {
                        $p_name = ":area_" . $i;
                        $area_placeholders[] = $p_name;
                        $named_params[$p_name] = trim($area);
                    }
                    $filtros[] = "TRIM(u.area) IN (" . implode(',', $area_placeholders) . ")";
                }
                if (!empty($usuarios)) {
                    $user_placeholders = [];
                    foreach ($usuarios as $i => $uid) {
                        $p_name = ":uid_" . $i;
                        $user_placeholders[] = $p_name;
                        $named_params[$p_name] = $uid;
                    }
                    $filtros[] = "u.id IN (" . implode(',', $user_placeholders) . ")";
                }

                if (!empty($filtros)) {
                    $sql .= " AND (" . implode(" OR ", $filtros) . ")";
                } else {
                    return []; // Config existe pero vacía
                }
            }
        }

        $sql .= " ORDER BY s.estado = 'pendiente' DESC, s.creado_en DESC";
        
        $this->db->query($sql);
        foreach ($named_params as $param => $val) {
            $this->db->bind($param, $val);
        }

        return $this->db->resultSet();
    }

    // Obtener motivos de permiso
    public function obtenerMotivos() {
        $this->db->query("SELECT * FROM motivos_permiso WHERE activo = 1");
        return $this->db->resultSet();
    }

    // Obtener motivos permitidos para un usuario específico (para el form de solicitar)
    public function obtenerMotivosParaUsuario($usuario_id) {
        // Primero obtener el area del usuario
        $this->db->query("SELECT area FROM usuarios WHERE id = :id");
        $this->db->bind(':id', $usuario_id);
        $user = $this->db->single();
        $user_area = $user ? $user->area : '';

        $todos = $this->obtenerMotivos();
        $permitidos = [];

        foreach ($todos as $motivo) {
            // Si no está visible para usuarios en general, saltar
            if ($motivo->visible_para_usuarios == 0) continue;

            $areas_permitidas = $motivo->areas_permitidas ? json_decode($motivo->areas_permitidas, true) : [];
            $usuarios_permitidos = $motivo->usuarios_permitidos ? json_decode($motivo->usuarios_permitidos, true) : [];

            $tiene_restriccion = (!empty($areas_permitidas) || !empty($usuarios_permitidos));

            if (!$tiene_restriccion) {
                $permitidos[] = $motivo;
            } else {
                $permitido_por_area = !empty($areas_permitidas) && in_array($user_area, $areas_permitidas);
                $permitido_por_usuario = !empty($usuarios_permitidos) && in_array((string)$usuario_id, $usuarios_permitidos);

                if ($permitido_por_area || $permitido_por_usuario) {
                    $permitidos[] = $motivo;
                }
            }
        }

        return $permitidos;
    }

    // Crear nueva solicitud
    public function crearSolicitud($data) {
        $this->db->query("INSERT INTO solicitudes_permiso (
            usuario_id, motivo_id, fecha_permiso, hora_permiso, horas_solicitadas, 
            regresa_laborar, firma_digital, requiere_reposicion, reposicion_fecha, 
            reposicion_hora, reposicion_observacion, soporte_nombre, estado
        ) VALUES (
            :usuario_id, :motivo_id, :fecha_permiso, :hora_permiso, :horas_solicitadas, 
            :regresa_laborar, :firma_digital, :requiere_reposicion, :reposicion_fecha, 
            :reposicion_hora, :reposicion_observacion, :soporte_nombre, 'pendiente'
        )");
        
        $this->db->bind(':usuario_id', $data['usuario_id']);
        $this->db->bind(':motivo_id', $data['motivo_id']);
        $this->db->bind(':fecha_permiso', $data['fecha_permiso']);
        $this->db->bind(':hora_permiso', $data['hora_permiso']);
        $this->db->bind(':horas_solicitadas', $data['horas_solicitadas']);
        $this->db->bind(':regresa_laborar', $data['regresa_laborar']);
        $this->db->bind(':firma_digital', $data['firma_digital']);
        $this->db->bind(':requiere_reposicion', $data['requiere_reposicion']);
        $this->db->bind(':reposicion_fecha', $data['reposicion_fecha']);
        $this->db->bind(':reposicion_hora', $data['reposicion_hora']);
        $this->db->bind(':reposicion_observacion', $data['reposicion_observacion']);
        $this->db->bind(':soporte_nombre', $data['soporte_nombre']);

        return $this->db->execute();
    }

    // Aprobar solicitud con firma
    public function aprobarSolicitud($id, $firma, $autorizador_id) {
        $this->db->query("UPDATE solicitudes_permiso SET estado = 'aprobada', firma_autorizacion = :firma, autorizado_por = :autorizador_id, autorizado_en = NOW() WHERE id = :id");
        $this->db->bind(':firma', $firma);
        $this->db->bind(':autorizador_id', $autorizador_id);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Rechazar solicitud con firma
    public function rechazarSolicitud($id, $firma, $autorizador_id) {
        $this->db->query("UPDATE solicitudes_permiso SET estado = 'rechazada', firma_rechazo = :firma, autorizado_por = :autorizador_id, autorizado_en = NOW() WHERE id = :id");
        $this->db->bind(':firma', $firma);
        $this->db->bind(':autorizador_id', $autorizador_id);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Guardar firma de regreso del empleado
    public function firmarRegresoEmpleado($id, $firma) {
        $this->db->query("UPDATE solicitudes_permiso SET firma_regreso_empleado = :firma WHERE id = :id");
        $this->db->bind(':firma', $firma);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Guardar firma de confirmación de regreso del autorizador
    public function confirmarRegresoAutorizador($id, $firma) {
        $this->db->query("UPDATE solicitudes_permiso SET firma_regreso_autorizador = :firma WHERE id = :id");
        $this->db->bind(':firma', $firma);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Obtener una solicitud por ID
    public function obtenerPorId($id) {
        $this->db->query("SELECT * FROM solicitudes_permiso WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Actualizar solicitud
    public function actualizarSolicitud($data) {
        $this->db->query("UPDATE solicitudes_permiso SET 
            motivo_id = :motivo_id, 
            fecha_permiso = :fecha_permiso, 
            hora_permiso = :hora_permiso, 
            horas_solicitadas = :horas_solicitadas, 
            regresa_laborar = :regresa_laborar, 
            firma_digital = :firma_digital, 
            requiere_reposicion = :requiere_reposicion, 
            reposicion_fecha = :reposicion_fecha, 
            reposicion_hora = :reposicion_hora, 
            reposicion_observacion = :reposicion_observacion, 
            soporte_nombre = :soporte_nombre
            WHERE id = :id AND estado = 'pendiente'");
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':motivo_id', $data['motivo_id']);
        $this->db->bind(':fecha_permiso', $data['fecha_permiso']);
        $this->db->bind(':hora_permiso', $data['hora_permiso']);
        $this->db->bind(':horas_solicitadas', $data['horas_solicitadas']);
        $this->db->bind(':regresa_laborar', $data['regresa_laborar']);
        $this->db->bind(':firma_digital', $data['firma_digital']);
        $this->db->bind(':requiere_reposicion', $data['requiere_reposicion']);
        $this->db->bind(':reposicion_fecha', $data['reposicion_fecha']);
        $this->db->bind(':reposicion_hora', $data['reposicion_hora']);
        $this->db->bind(':reposicion_observacion', $data['reposicion_observacion']);
        $this->db->bind(':soporte_nombre', $data['soporte_nombre']);

        return $this->db->execute();
    }
}
