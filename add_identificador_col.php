<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c->query("ALTER TABLE alertas ADD COLUMN identificador_evento VARCHAR(255) DEFAULT NULL AFTER borrada");
    $c->query("CREATE INDEX idx_identificador_evento ON alertas(identificador_evento)");
    echo "Columna 'identificador_evento' agregada correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>