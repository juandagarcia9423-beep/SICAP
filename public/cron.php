<?php
// Cargar la configuración y el cargador automático
require_once '../config/config.php';
require_once '../app/Core/Database.php';
require_once '../app/Models/Alerta.php';
require_once '../app/Helpers/AutomacionHelper.php';

// Ejecutar las tareas de automatización sin restricciones de tiempo
// (En el cron ignoramos el límite de 1 minuto para asegurar ejecución)
app\Helpers\AutomacionHelper::ejecutarTareasCron();

echo "Tareas de automatización completadas: " . date('Y-m-d H:i:s') . "\n";
