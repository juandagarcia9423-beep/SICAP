<?php
try {
    $c = new PDO('mysql:host=localhost;dbname=sicap_db', 'root', '');
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c->exec("ALTER TABLE solicitudes_permiso ADD COLUMN firma_rechazo LONGTEXT NULL AFTER firma_autorizacion");
    echo "Column firma_rechazo added successfully.";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column name')) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
