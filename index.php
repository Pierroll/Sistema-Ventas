<?php
declare(strict_types=1);

session_start();

// Error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');

// Cargar Composer autoload
require 'vendor/autoload.php';

// Cargar configuración
require_once 'Config/Config.php';

// Constantes locales
define('HEADER_LOCATION', 'Location: ');
define('ERROR_ROUTE', 'errors');

// Sanitizar y procesar URL
$ruta = !empty($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : "home/index";
$array = explode("/", $ruta);

$controller = isset($array[0]) ? ucfirst(strClean($array[0])) : "Home";
$metodo = isset($array[1]) && !empty($array[1]) ? strClean($array[1]) : "index";
$parametro = "";

// Procesar parámetros adicionales
if (isset($array[2]) && !empty($array[2])) {
    $parametros = array_slice($array, 2);
    $parametro = implode(",", array_map('strClean', $parametros));
}

// Cargar controlador
$dirControllers = "Controllers/" . $controller . ".php";

if (file_exists($dirControllers)) {
    require_once $dirControllers;

    if (class_exists($controller)) {
        $objController = new $controller();

        if (method_exists($objController, $metodo)) {
            $objController->$metodo($parametro);
        } else {
            header(HEADER_LOCATION . BASE_URL . ERROR_ROUTE);
        }
    } else {
        header(HEADER_LOCATION . BASE_URL . ERROR_ROUTE);
    }
} else {
    header(HEADER_LOCATION . BASE_URL . ERROR_ROUTE);
}
