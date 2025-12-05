<?php
if (!defined('BASE_URL')) {
    $protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https")) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . $host . '/venta/');
}
if (!defined('HOST')) {
    define('HOST', '127.0.0.1');
}
if (!defined('USER')) {
    define('USER', 'root');
}
if (!defined('PASS')) {
    define('PASS', '');
}
if (!defined('DB')) {
    define('DB', 'venta');
}
if (!defined('CHARSET')) {
    define('CHARSET', 'charset=utf8');
}
if (!defined('HOST_SMTP')) {
    define('HOST_SMTP', 'smtp.gmail.com');
}
if (!defined('USER_SMTP')) {
    define('USER_SMTP', 'correo@gmail.com');
}
if (!defined('CLAVE_SMTP')) {
    define('CLAVE_SMTP', 'clave_correo');
}
if (!defined('PUERTO_SMTP')) {
    define('PUERTO_SMTP', 465);
}
if (!defined('TITLE')) {
    define('TITLE', 'SISTEMA VENTA');
}
?>
