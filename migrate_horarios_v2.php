<?php
require_once 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check if 'tipo_personal' column exists in 'usuarios'
    $stmt = $c->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('tipo_personal', $columns)) {
        $c->exec("ALTER TABLE usuarios ADD COLUMN tipo_personal ENUM('planta', 'administrativo') DEFAULT 'planta'");
        echo "Column 'tipo_personal' added to 'usuarios'.\n";
    } else {
        echo "Column 'tipo_personal' already exists in 'usuarios'.\n";
    }

    // 2. Adjust 'configuracion_horarios': Drop 'area' and add 'tipo_personal'
    $stmt = $c->query("DESCRIBE configuracion_horarios");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('area', $columns)) {
        $c->exec("ALTER TABLE configuracion_horarios DROP COLUMN area");
        echo "Column 'area' dropped from 'configuracion_horarios'.\n";
    }
    
    if (!in_array('tipo_personal', $columns)) {
        $c->exec("ALTER TABLE configuracion_horarios ADD COLUMN tipo_personal ENUM('planta', 'administrativo') NOT NULL");
        echo "Column 'tipo_personal' added to 'configuracion_horarios'.\n";
    }
    
    // 3. Update unique index
    $c->exec("ALTER TABLE configuracion_horarios DROP INDEX unique_area_dia");
    $c->exec("ALTER TABLE configuracion_horarios ADD UNIQUE KEY unique_tipo_dia (tipo_personal, dia_semana)");
    echo "Unique index updated.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
