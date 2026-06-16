<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c->query("DELETE FROM alertas");
    $c->query("DELETE FROM eventos_descartados");
    echo "Limpieza de pruebas completada. Tablas listas para nuevos identificadores.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>