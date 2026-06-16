<?php
require_once 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Table: configuracion_autorizadores\n";
    $stmt = $c->query("DESCRIBE configuracion_autorizadores");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\nTable: motivos_permiso\n";
    $stmt = $c->query("DESCRIBE motivos_permiso");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
