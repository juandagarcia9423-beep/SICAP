<?php
require 'config/config.php';
try {
    $c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    print_r($c->query("DESCRIBE banco_horas_movimientos")->fetchAll(PDO::FETCH_ASSOC));
    print_r($c->query("DESCRIBE usuarios")->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>