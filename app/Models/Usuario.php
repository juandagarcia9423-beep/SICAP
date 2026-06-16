<?php
namespace app\Models;

use app\Core\Database;

class Usuario {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Buscar usuario por nombre de usuario o email
    public function buscarUsuario($identificador) {
        $this->db->query('SELECT * FROM usuarios WHERE usuario = :id OR email = :id');
        $this->db->bind(':id', $identificador);
        return $this->db->single();
    }

    // Login de usuario
    public function login($identificador, $password) {
        $row = $this->buscarUsuario($identificador);

        if ($row) {
            $hashed_password = $row->password_hash;
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        return false;
    }

    // Registrar nuevo usuario
    public function registrar($data) {
        $this->db->query('INSERT INTO usuarios (nombre, usuario, cedula, email, password_hash, rol, area, tipo_jornada, tipo_personal, permite_pin, permite_facial, permite_qr, pin_secreto, foto_facial, dias_cambio_password, alerta_cambio_password, ultimo_cambio_password, huella) 
                          VALUES (:nombre, :usuario, :cedula, :email, :password_hash, :rol, :area, :tipo_jornada, :tipo_personal, :permite_pin, :permite_facial, :permite_qr, :pin_secreto, :foto_facial, :dias_cambio_password, :alerta_cambio_password, CURRENT_TIMESTAMP, "")');
        
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':usuario', $data['usuario']);
        $this->db->bind(':cedula', $data['cedula']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', $data['password_hash']);
        $this->db->bind(':rol', $data['rol']);
        $this->db->bind(':area', $data['area']);
        $this->db->bind(':tipo_jornada', $data['tipo_jornada']);
        $this->db->bind(':tipo_personal', $data['tipo_personal']);
        $this->db->bind(':permite_pin', $data['permite_pin'] ? 1 : 0);
        $this->db->bind(':permite_facial', $data['permite_facial'] ? 1 : 0);
        $this->db->bind(':permite_qr', $data['permite_qr'] ? 1 : 0);
        $this->db->bind(':pin_secreto', $data['pin_secreto']);
        $this->db->bind(':foto_facial', $data['foto_facial'] ?? null);
        $this->db->bind(':dias_cambio_password', $data['dias_cambio_password']);
        $this->db->bind(':alerta_cambio_password', $data['alerta_cambio_password']);

        return $this->db->execute();
    }

    // Obtener usuario por ID
    public function obtenerUsuarioPorId($id) {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Actualizar usuario
    public function actualizar($data) {
        $sql = 'UPDATE usuarios SET nombre = :nombre, usuario = :usuario, cedula = :cedula, email = :email, rol = :rol, area = :area, tipo_jornada = :tipo_jornada, tipo_personal = :tipo_personal, permite_pin = :permite_pin, permite_facial = :permite_facial, permite_qr = :permite_qr, pin_secreto = :pin_secreto, foto_facial = :foto_facial, dias_cambio_password = :dias_cambio_password, alerta_cambio_password = :alerta_cambio_password';
        
        if (!empty($data['password_hash'])) {
            $sql .= ', password_hash = :password_hash, ultimo_cambio_password = CURRENT_TIMESTAMP';
        } elseif (!empty($data['ultimo_cambio_password'])) {
            $sql .= ', ultimo_cambio_password = :ultimo_cambio_password';
        }
        
        $sql .= ' WHERE id = :id';
        
        $this->db->query($sql);
        
        if (!empty($data['password_hash'])) {
            $this->db->bind(':password_hash', $data['password_hash']);
        } elseif (!empty($data['ultimo_cambio_password'])) {
            $this->db->bind(':ultimo_cambio_password', $data['ultimo_cambio_password']);
        }

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':usuario', $data['usuario']);
        $this->db->bind(':cedula', $data['cedula']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':rol', $data['rol']);
        $this->db->bind(':area', $data['area']);
        $this->db->bind(':tipo_jornada', $data['tipo_jornada']);
        $this->db->bind(':tipo_personal', $data['tipo_personal']);
        $this->db->bind(':permite_pin', $data['permite_pin'] ? 1 : 0);
        $this->db->bind(':permite_facial', $data['permite_facial'] ? 1 : 0);
        $this->db->bind(':permite_qr', $data['permite_qr'] ? 1 : 0);
        $this->db->bind(':pin_secreto', $data['pin_secreto']);
        $this->db->bind(':foto_facial', $data['foto_facial'] ?? null);
        $this->db->bind(':dias_cambio_password', $data['dias_cambio_password']);
        $this->db->bind(':alerta_cambio_password', $data['alerta_cambio_password']);

        return $this->db->execute();
    }

    // Eliminar usuario
    public function eliminar($id) {
        $this->db->query('DELETE FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function obtenerUsuarios() {
        $this->db->query("SELECT id, nombre, cedula, area FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
        return $this->db->resultSet();
    }

    public function obtenerSaldo($id) {
        $this->db->query("SELECT saldo_horas FROM usuarios WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row ? (float)$row->saldo_horas : 0.0;
    }
}
