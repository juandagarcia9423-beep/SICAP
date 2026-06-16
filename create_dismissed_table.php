<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Crear tabla de eventos descartados para permitir borrado físico
    $c->query("CREATE TABLE IF NOT EXISTS eventos_descartados (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT UNSIGNED NOT NULL,
        tipo_alerta VARCHAR(50) NOT NULL,
        identificador_evento VARCHAR(255) NOT NULL,
        fecha_descarte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY idx_unique_event (usuario_id, tipo_alerta, identificador_evento)
    )");
    
    echo "Tabla eventos_descartados creada/verificada correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>