<?php
$m = (object)['id'=>1, 'nombre'=>'Test', 'descripcion' => "Test con 'comilla' y \"doble\""];
echo "<button onclick='editarMotivo(" . htmlspecialchars(json_encode($m), ENT_QUOTES, "UTF-8") . ")'></button>";
?>