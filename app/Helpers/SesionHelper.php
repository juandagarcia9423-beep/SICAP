<?php
namespace app\Helpers;

class SesionHelper {
    // Iniciar sesión si no está iniciada
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            }
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

        // Ejecutar tareas automáticas (inasistencias, alertas, etc)
        AutomacionHelper::ejecutarTareas();
    }

    // Verificar si el usuario tiene un permiso específico
    public static function tienePermiso($modulo, $accion) {
        self::init();
        if (!isset($_SESSION['usuario_id'])) return false;
        
        // El Superadmin siempre tiene todos los permisos
        if ($_SESSION['usuario_rol'] == 'superadmin') return true;

        $db = new \app\Core\Database();

        // Si es el módulo de permisos y la acción es ver o editar, 
        // verificar si es un autorizador configurado
        if ($modulo == 'permisos' && ($accion == 'ver' || $accion == 'editar')) {
            $db->query("SELECT COUNT(*) as total FROM configuracion_autorizadores WHERE autorizador_id = :usuario_id");
            $db->bind(':usuario_id', $_SESSION['usuario_id']);
            if ($db->single()->total > 0) return true;
        }

        $db->query("SELECT COUNT(*) as total 
                    FROM usuarios_permisos up 
                    JOIN permisos p ON up.permiso_id = p.id 
                    WHERE up.usuario_id = :usuario_id 
                    AND p.modulo = :modulo 
                    AND p.accion = :accion");
        $db->bind(':usuario_id', $_SESSION['usuario_id']);
        $db->bind(':modulo', $modulo);
        $db->bind(':accion', $accion);
        
        return $db->single()->total > 0;
    }
}
