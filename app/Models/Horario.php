<?php
namespace app\Models;

use app\Core\Database;

class Horario {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function obtenerTurnos() {
        $this->db->query("SELECT * FROM turnos");
        return $this->db->resultSet();
    }

    public function obtenerConfiguraciones($tipo_personal) {
        $this->db->query("SELECT * FROM configuracion_horarios WHERE tipo_personal = :tipo_personal ORDER BY turno_nombre, dia_semana");
        $this->db->bind(':tipo_personal', $tipo_personal);
        return $this->db->resultSet();
    }

    public function guardarConfiguracion($data) {
        $this->db->query("INSERT INTO configuracion_horarios (tipo_personal, dia_semana, turno_nombre, hora_entrada, hora_salida, horas_ordinarias, activo) 
                          VALUES (:tipo_personal, :dia_semana, :turno_nombre, :hora_entrada, :hora_salida, :horas_ordinarias, :activo)
                          ON DUPLICATE KEY UPDATE hora_entrada = :hora_entrada, hora_salida = :hora_salida, horas_ordinarias = :horas_ordinarias, activo = :activo");
        
        $this->db->bind(':tipo_personal', $data['tipo_personal']);
        $this->db->bind(':dia_semana', $data['dia_semana']);
        $this->db->bind(':turno_nombre', $data['turno_nombre']);
        $this->db->bind(':hora_entrada', $data['hora_entrada']);
        $this->db->bind(':hora_salida', $data['hora_salida']);
        $this->db->bind(':horas_ordinarias', $data['horas_ordinarias']);
        $this->db->bind(':activo', $data['activo'] ? 1 : 0);

        return $this->db->execute();
    }
}
