<?php
namespace app\Models;

use app\Core\Database;

class Asistencia {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Obtener marcaciones con filtros opcionales (Estructura Sincronizada)
    public function obtenerMarcaciones($filtros = []) {
        $sql = "SELECT a.id, a.usuario_id, a.tipo, a.registrado_en, u.nombre, u.cedula, u.area as nombre_area 
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

        $sql .= " ORDER BY a.registrado_en DESC";

        // Usando PDO directa para asegurar compatibilidad total
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Obtener el turno configurado para un tipo de personal y día
    public function obtenerTurnoParaUsuario($usuario_id, $dia_semana) {
        $this->db->query("SELECT ch.hora_entrada, ch.hora_salida, ch.horas_ordinarias 
                          FROM configuracion_horarios ch 
                          JOIN usuarios u ON u.tipo_personal = ch.tipo_personal
                          WHERE u.id = :usuario_id AND ch.dia_semana = :dia_semana AND ch.activo = 1");
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':dia_semana', $dia_semana);
        return $this->db->single();
    }

    // Registrar una marcación (Adaptado a nueva estructura)
    public function obtenerMarcacionPorId($id) {
        $this->db->query("SELECT * FROM asistencia WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function actualizarMarcacion($data) {
        $this->db->query("UPDATE asistencia SET tipo = :tipo, registrado_en = :registrado_en WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':tipo', $data['tipo']);
        $this->db->bind(':registrado_en', $data['registrado_en']);
        return $this->db->execute();
    }

    public function registrarEvento($usuario_id, $tipo, $area_id = 0) {
        $this->db->query("INSERT INTO asistencia (usuario_id, area, tipo) VALUES (:usuario_id, :area, :tipo)");
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':area', $area_id);
        $this->db->bind(':tipo', $tipo);
        return $this->db->execute();
    }

    public function eliminarMarcacion($id) {
        $this->db->query("DELETE FROM asistencia WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
