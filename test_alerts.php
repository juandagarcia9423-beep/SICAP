<?php
require 'config/config.php';
$c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
print_r($c->query('DESCRIBE alertas')->fetchAll(PDO::FETCH_ASSOC));
?>