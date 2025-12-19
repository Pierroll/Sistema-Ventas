<?php

echo "===== TEST DE CONEXIÓN A BASE DE DATOS =====\n";
echo "Tiempo: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Cargar configuración
echo "1️⃣ Cargando configuración...\n";
require 'Config/Config.php';

echo "   BASE_URL: " . BASE_URL . "\n";
echo "   HOST: " . HOST . "\n";
echo "   USER: " . USER . "\n";
echo "   DB: " . DB . "\n";
echo "   ✓ Configuración cargada\n\n";

// 2. Verificar extensión PDO
echo "2️⃣ Verificando extensión PDO...\n";
if (extension_loaded('pdo')) {
    echo "   ✓ PDO cargado\n";
} else {
    echo "   ✗ PDO NO está disponible\n";
    die("Error: PDO extension no está instalada\n");
}

if (extension_loaded('pdo_mysql')) {
    echo "   ✓ PDO MySQL cargado\n";
} else {
    echo "   ✗ PDO MySQL NO está disponible\n";
    die("Error: PDO MySQL extension no está instalada\n");
}
echo "\n";

// 3. Intentar conexión con localhost
echo "3️⃣ Intentando conexión con HOST='localhost'...\n";
$dsn_localhost = "mysql:host=localhost;port=3306;dbname=" . DB . ";" . CHARSET;
echo "   DSN: $dsn_localhost\n";
try {
    $pdo_localhost = new PDO($dsn_localhost, USER, PASS);
    echo "   ✓ Conexión EXITOSA con localhost\n";
    $pdo_localhost = null;
} catch (PDOException $e) {
    echo "   ✗ Conexión FALLIDA con localhost\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Código: " . $e->getCode() . "\n";
}
echo "\n";

// 4. Intentar conexión con 127.0.0.1
echo "4️⃣ Intentando conexión con HOST='127.0.0.1'...\n";
$dsn_ip = "mysql:host=127.0.0.1;port=3306;dbname=" . DB . ";" . CHARSET;
echo "   DSN: $dsn_ip\n";
try {
    $pdo_ip = new PDO($dsn_ip, USER, PASS);
    echo "   ✓ Conexión EXITOSA con 127.0.0.1\n";
    $pdo_ip = null;
} catch (PDOException $e) {
    echo "   ✗ Conexión FALLIDA con 127.0.0.1\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Código: " . $e->getCode() . "\n";
}
echo "\n";

// 5. Verificar estado de MySQL
echo "5️⃣ Verificando estado de MySQL...\n";
$output = shell_exec("ps aux | grep -i mysql | grep -v grep");
if ($output) {
    echo "   ✓ MySQL está corriendo\n";
    echo "   Procesos:\n";
    foreach (explode("\n", trim($output)) as $line) {
        if ($line) {
            echo "   " . substr($line, 0, 100) . "...\n";
        }
    }
} else {
    echo "   ✗ MySQL NO está corriendo (o no encontrado en ps)\n";
}
echo "\n";

// 6. Verificar socket Unix
echo "6️⃣ Verificando sockets Unix...\n";
$sockets = [
    '/tmp/mysql.sock',
    '/var/run/mysqld/mysql.sock',
    '/var/tmp/mysql.sock'
];
foreach ($sockets as $socket) {
    if (file_exists($socket)) {
        echo "   ✓ Socket encontrado: $socket\n";
    } else {
        echo "   ✗ Socket no existe: $socket\n";
    }
}
echo "\n";

// 7. Resumen
echo "====== RESUMEN ======\n";
echo "📊 Para arreglar el error:\n";
echo "   - Si MySQL NO está corriendo: /Applications/XAMPP/xamppfiles/xampp startmysql\n";
echo "   - Si localhost falla pero 127.0.0.1 funciona: Cambiar HOST a 127.0.0.1\n";
echo "   - Si la BD 'venta' no existe: Crear con phpMyAdmin o MySQL CLI\n";
echo "\n";

?>