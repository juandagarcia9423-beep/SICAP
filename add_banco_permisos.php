<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insertar permisos para el módulo banco_horas
    $acciones = ['ver', 'crear', 'editar', 'eliminar', 'configurar'];
    foreach ($acciones as $accion) {
        $stmt = $c->prepare("INSERT IGNORE INTO permisos (modulo, accion) VALUES ('bancohoras', :accion)");
        $stmt->execute([':accion' => $accion]);
    }
    
    echo "Permisos de 'bancohoras' agregados correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>