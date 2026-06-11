<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

$db = new \app\Core\Database();
$db->query("SELECT s.id, s.estado, s.autorizado_por, a.nombre as autorizador_nombre 
            FROM solicitudes_permiso s 
            LEFT JOIN usuarios a ON s.autorizado_por = a.id
            WHERE s.estado != 'pendiente'");
$results = $db->resultSet();
print_r($results);
?>