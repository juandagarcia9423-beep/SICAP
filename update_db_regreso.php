<?php
try {
    $c = new PDO('mysql:host=localhost;dbname=sicap_db', 'root', '');
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c->exec("ALTER TABLE solicitudes_permiso 
              ADD COLUMN firma_regreso_empleado LONGTEXT NULL AFTER firma_autorizacion,
              ADD COLUMN firma_regreso_autorizador LONGTEXT NULL AFTER firma_regreso_empleado");
    echo "Columns added successfully.";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column name')) {
        echo "Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
