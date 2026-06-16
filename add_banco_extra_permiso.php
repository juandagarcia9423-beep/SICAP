<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Agregar permiso 'ver_propio' al módulo bancohoras
    $stmt = $c->prepare("INSERT IGNORE INTO permisos (modulo, accion) VALUES ('bancohoras', 'ver_propio')");
    $stmt->execute();
    
    echo "Permiso 'ver_propio' para 'bancohoras' agregado.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>