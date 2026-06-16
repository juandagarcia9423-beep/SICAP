<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $permisos = $c->query('SELECT * FROM permisos')->fetchAll(PDO::FETCH_ASSOC);
    echo "Permisos actuales:\n";
    print_r($permisos);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>