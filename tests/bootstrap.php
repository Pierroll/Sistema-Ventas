<?php
declare(strict_types=1);

// Cargar autoload de Composer por si hay alguna clase con namespace
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload manual para clases sin namespace (como Controller, Query, etc.)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../Config/App/' . $class . '.php',
        __DIR__ . '/../Controllers/' . $class . '.php',
        __DIR__ . '/../Models/' . $class . '.php'
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Constantes necesarias
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8000/');
}

// Cargar helpers y configuración
require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../Config/Helpers.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');
