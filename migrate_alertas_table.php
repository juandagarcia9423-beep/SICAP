<?php
require_once 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create 'alertas' table
    $c->exec("CREATE TABLE IF NOT EXISTS alertas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        tipo_alerta VARCHAR(50) NOT NULL,
        descripcion TEXT,
        fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )");
    echo "Table 'alertas' created.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
