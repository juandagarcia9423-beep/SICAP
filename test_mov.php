<?php
require 'config/config.php';
$c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$movs = $c->query('SELECT * FROM banco_horas_movimientos ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
print_r($movs);
$users = $c->query('SELECT id, nombre, saldo_horas FROM usuarios WHERE id = ' . ($movs[0]['usuario_id'] ?? 0))->fetchAll(PDO::FETCH_ASSOC);
print_r($users);
?>