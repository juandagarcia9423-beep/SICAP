<?php
namespace app\Models;

use app\Core\Database;

class Alerta {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function obtenerAlertas($filtros = [], $limit = 15, $offset = 0) {
        $sql = "SELECT a.*, u.nombre as usuario_nombre, u.area as usuario_area
                FROM alertas a 
                JOIN usuarios u ON a.usuario_id = u.id 
                WHERE 1=1";
        
        if (!empty($filtros['tipo'])) {
            $sql .= " AND a.tipo_alerta = :tipo";
        }
        $sql .= " ORDER BY a.fecha_alerta DESC LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        if (!empty($filtros['tipo'])) {
            $this->db->bind(':tipo', $filtros['tipo']);
        }
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function contarAlertas($filtros = []) {
        $sql = "SELECT COUNT(*) FROM alertas a WHERE 1=1";
        if (!empty($filtros['tipo'])) {
            $sql .= " AND a.tipo_alerta = :tipo";
        }
        $this->db->query($sql);
        if (!empty($filtros['tipo'])) {
            $this->db->bind(':tipo', $filtros['tipo']);
        }
        return $this->db->fetchColumn();
    }

    public function alertaYaExiste($usuario_id, $tipo, $fecha) {
        $this->db->query("SELECT id FROM alertas 
                          WHERE usuario_id = :usuario_id 
                          AND tipo_alerta = :tipo 
                          AND DATE(fecha_alerta) = :fecha");
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':fecha', $fecha);
        return $this->db->single() !== false;
    }

    public function registrarAlerta($usuario_id, $tipo, $descripcion) {
        // Solo registrar si no existe una alerta del mismo tipo hoy
        $fechaHoy = date('Y-m-d');
        if ($this->alertaYaExiste($usuario_id, $tipo, $fechaHoy)) {
            return false;
        }

        $this->db->query("INSERT INTO alertas (usuario_id, tipo_alerta, descripcion) VALUES (:usuario_id, :tipo_alerta, :descripcion)");
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':tipo_alerta', $tipo);
        $this->db->bind(':descripcion', $descripcion);
        return $this->db->execute();
    }

    public function obtenerContadorAlertas() {
        $this->db->query("SELECT tipo_alerta, COUNT(*) as total FROM alertas WHERE leido = FALSE GROUP BY tipo_alerta");
        return $this->db->resultSet();
    }

    public function eliminarAlerta($id) {
        $this->db->query("DELETE FROM alertas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function toggleLeida($id) {
        $this->db->query("UPDATE alertas SET leido = NOT leido WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
