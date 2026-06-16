<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c->query("ALTER TABLE alertas ADD COLUMN borrada TINYINT(1) DEFAULT 0 AFTER leido");
    echo "Columna 'borrada' agregada correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>