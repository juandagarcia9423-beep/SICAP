<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";

try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa.<br>";

    $queries = [
        'ALTER TABLE configuracion_autorizadores MODIFY areas_permitidas TEXT',
        'ALTER TABLE configuracion_autorizadores MODIFY usuarios_permitidos TEXT',
        'ALTER TABLE motivos_permiso MODIFY areas_permitidas TEXT',
        'ALTER TABLE motivos_permiso MODIFY usuarios_permitidos TEXT'
    ];

    foreach ($queries as $sql) {
        echo "Ejecutando: $sql... ";
        $c->exec($sql);
        echo "OK.<br>";
    }
    
    echo "<strong>Migración exitosa: Columnas modificadas a TEXT.</strong><br>";
} catch (PDOException $e) {
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
}
?>
