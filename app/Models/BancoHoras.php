<?php
namespace app\Models;

use app\Core\Database;

class BancoHoras {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function obtenerMovimientos($usuario_id = null, $limit = 10, $offset = 0, $soloDeudores = false) {
        $sql = "SELECT m.*, u.nombre as empleado_nombre, a.nombre as autorizador_nombre 
                FROM banco_horas_movimientos m 
                JOIN usuarios u ON m.usuario_id = u.id 
                LEFT JOIN usuarios a ON m.autorizado_por = a.id";
        
        $where = [];
        if ($usuario_id) $where[] = "m.usuario_id = :usuario_id";
        if ($soloDeudores) $where[] = "u.saldo_horas < 0";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY m.fecha_movimiento DESC LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        if ($usuario_id) {
            $this->db->bind(':usuario_id', $usuario_id);
        }
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function contarMovimientos($usuario_id = null, $soloDeudores = false) {
        $sql = "SELECT COUNT(*) FROM banco_horas_movimientos m";
        if ($soloDeudores) {
            $sql .= " JOIN usuarios u ON m.usuario_id = u.id";
        }

        $where = [];
        if ($usuario_id) $where[] = "m.usuario_id = :usuario_id";
        if ($soloDeudores) $where[] = "u.saldo_horas < 0";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $this->db->query($sql);
        if ($usuario_id) {
            $this->db->bind(':usuario_id', $usuario_id);
        }
        return $this->db->fetchColumn();
    }

    public function registrarMovimiento($data) {
        // 1. Registrar movimiento en el histórico
        $this->db->query("INSERT INTO banco_horas_movimientos (usuario_id, tipo, horas, concepto, autorizado_por) 
                          VALUES (:usuario_id, :tipo, :horas, :concepto, :autorizado_por)");
        $this->db->bind(':usuario_id', $data['usuario_id']);
        $this->db->bind(':tipo', $data['tipo']);
        $this->db->bind(':horas', $data['horas']);
        $this->db->bind(':concepto', $data['concepto']);
        $this->db->bind(':autorizado_por', $data['autorizado_por']);
        
        if (!$this->db->execute()) return false;

        // 2. Actualizar saldo en la tabla usuarios
        $operador = ($data['tipo'] == 'credito') ? '+' : '-';
        $this->db->query("UPDATE usuarios SET saldo_horas = saldo_horas $operador :horas WHERE id = :usuario_id");
        $this->db->bind(':horas', $data['horas']);
        $this->db->bind(':usuario_id', $data['usuario_id']);
        
        return $this->db->execute();
    }

    public function obtenerSaldo($usuario_id) {
        $this->db->query("SELECT saldo_horas FROM usuarios WHERE id = :usuario_id");
        $this->db->bind(':usuario_id', $usuario_id);
        $row = $this->db->single();
        return $row ? $row->saldo_horas : 0;
    }
}
