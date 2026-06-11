<?php
namespace app\Helpers;

class SesionHelper {
    // Iniciar sesión si no está iniciada
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Verificar si el usuario está logueado
    public static function estaLogueado() {
        self::init();
        return isset($_SESSION['usuario_id']);
    }

    // Redirigir si no está logueado
    public static function protegerRuta() {
        if (!self::estaLogueado()) {
            header('location: ' . URLROOT . '/auth/login');
            exit();
        }
    }
}
