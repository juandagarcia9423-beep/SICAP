<?php
require 'config/config.php';
$c = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$motivos = $c->query('SELECT * FROM motivos_permiso LIMIT 2')->fetchAll(PDO::FETCH_OBJ);
foreach($motivos as $m) {
    echo htmlspecialchars(json_encode($m), ENT_QUOTES, "UTF-8") . "\n";
}
?>