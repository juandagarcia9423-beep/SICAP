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

    /**
     * Verifica si una alerta YA existe en la tabla activa O si fue previamente eliminada (descartada)
     */
    public function alertaYaExiste($usuario_id, $tipo, $identificador_evento) {
        // 1. Verificar si ya está activa
        $this->db->query("SELECT id FROM alertas 
                          WHERE usuario_id = :usuario_id 
                          AND tipo_alerta = :tipo 
                          AND identificador_evento = :identificador");
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':identificador', $identificador_evento);
        if ($this->db->single()) return true;

        // 2. Verificar si fue descartada permanentemente
        $this->db->query("SELECT id FROM eventos_descartados 
                          WHERE usuario_id = :usuario_id 
                          AND tipo_alerta = :tipo 
                          AND identificador_evento = :identificador");
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':identificador', $identificador_evento);
        return $this->db->single() !== false;
    }

    public function registrarAlerta($usuario_id, $tipo, $descripcion, $identificador_evento, $fechaCustom = null) {
        // Doble verificación de seguridad
        if ($this->alertaYaExiste($usuario_id, $tipo, $identificador_evento)) {
            return false;
        }

        $sql = "INSERT INTO alertas (usuario_id, tipo_alerta, descripcion, identificador_evento" . ($fechaCustom ? ", fecha_alerta" : "") . ") 
                VALUES (:usuario_id, :tipo_alerta, :descripcion, :identificador" . ($fechaCustom ? ", :fecha" : "") . ")";
        
        $this->db->query($sql);
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':tipo_alerta', $tipo);
        $this->db->bind(':descripcion', $descripcion);
        $this->db->bind(':identificador', $identificador_evento);
        if ($fechaCustom) {
            $this->db->bind(':fecha', $fechaCustom);
        }
        return $this->db->execute();
    }

    public function obtenerContadorAlertas() {
        $this->db->query("SELECT tipo_alerta, COUNT(*) as total FROM alertas WHERE leido = FALSE GROUP BY tipo_alerta");
        return $this->db->resultSet();
    }

    public function eliminarAlerta($id) {
        // Antes de eliminar, guardamos en descartados para que no se recree
        $this->db->query("SELECT usuario_id, tipo_alerta, identificador_evento FROM alertas WHERE id = :id");
        $this->db->bind(':id', $id);
        $alerta = $this->db->single();

        if ($alerta) {
            $this->db->query("INSERT IGNORE INTO eventos_descartados (usuario_id, tipo_alerta, identificador_evento) 
                              VALUES (:usuario_id, :tipo, :identificador)");
            $this->db->bind(':usuario_id', $alerta->usuario_id);
            $this->db->bind(':tipo', $alerta->tipo_alerta);
            $this->db->bind(':identificador', $alerta->identificador_evento);
            $this->db->execute();
        }

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
