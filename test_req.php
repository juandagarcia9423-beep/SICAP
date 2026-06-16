<?php
require 'config/config.php';
$c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
print_r($c->query('SELECT id, motivo_id, horas_solicitadas, forma_pago, estado FROM solicitudes_permiso ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC));
?>