<?php
require_once 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $stmt = $c->query('DESCRIBE configuracion_autorizadores');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
