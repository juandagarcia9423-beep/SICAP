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

    // Obtener todas las solicitudes para autorizar
    public function obtenerTodasParaAutorizar() {
        $this->db->query("SELECT s.*, m.nombre as motivo_nombre, u.nombre as empleado_nombre, u.cedula, u.area, a.nombre as autorizador_nombre 
                          FROM solicitudes_permiso s 
                          JOIN motivos_permiso m ON s.motivo_id = m.id 
                          JOIN usuarios u ON s.usuario_id = u.id 
                          LEFT JOIN usuarios a ON s.autorizado_por = a.id
                          ORDER BY s.estado = 'pendiente' DESC, s.creado_en DESC");
        return $this->db->resultSet();
    }

    // Obtener motivos de permiso
    public function obtenerMotivos() {
        $this->db->query("SELECT id, nombre, descripcion, repone_tiempo, visible_para_usuarios FROM motivos_permiso WHERE activo = 1");
        return $this->db->resultSet();
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
