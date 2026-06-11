<?php
// Autocarga de clases básica
spl_autoload_register(function($className) {
    $file = dirname(__DIR__) . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once '../config/config.php';

// Inicializar el Router
$router = new app\Core\Router();
