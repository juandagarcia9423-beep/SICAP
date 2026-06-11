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
        $this->db->query('INSERT INTO usuarios (nombre, usuario, cedula, email, password_hash, rol, area, tipo_jornada, huella) VALUES (:nombre, :usuario, :cedula, :email, :password_hash, :rol, :area, :tipo_jornada, "")');
        
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':usuario', $data['usuario']);
        $this->db->bind(':cedula', $data['cedula']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', $data['password_hash']);
        $this->db->bind(':rol', $data['rol']);
        $this->db->bind(':area', $data['area']);
        $this->db->bind(':tipo_jornada', $data['tipo_jornada']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Obtener usuario por ID
    public function obtenerUsuarioPorId($id) {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Actualizar usuario
    public function actualizar($data) {
        if (!empty($data['password_hash'])) {
            $this->db->query('UPDATE usuarios SET nombre = :nombre, usuario = :usuario, cedula = :cedula, email = :email, password_hash = :password_hash, rol = :rol, area = :area, tipo_jornada = :tipo_jornada WHERE id = :id');
            $this->db->bind(':password_hash', $data['password_hash']);
        } else {
            $this->db->query('UPDATE usuarios SET nombre = :nombre, usuario = :usuario, cedula = :cedula, email = :email, rol = :rol, area = :area, tipo_jornada = :tipo_jornada WHERE id = :id');
        }

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':usuario', $data['usuario']);
        $this->db->bind(':cedula', $data['cedula']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':rol', $data['rol']);
        $this->db->bind(':area', $data['area']);
        $this->db->bind(':tipo_jornada', $data['tipo_jornada']);

        return $this->db->execute();
    }

    // Eliminar usuario
    public function eliminar($id) {
        $this->db->query('DELETE FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
