<?php
// Copia esto en un archivo llamado debug_strclean.php en la raíz del proyecto
// Ejecuta: php debug_strclean.php

$input = "SELECT * FROM users WHERE id = 1 OR '1'='1'";
$expected = "users WHERE id = 1 OR ";

echo "📥 INPUT: $input\n";
echo "✅ ESPERADO: $expected\n";
echo "\n";

// Simulemos paso a paso qué hace str_ireplace
$string = $input;

echo "=== PASO A PASO ===\n";
echo "Inicial: " . var_export($string, true) . "\n";

// Paso 1: Eliminar SELECT * FROM
$string = str_ireplace('SELECT * FROM', '', $string);
echo "Después de 'SELECT * FROM': " . var_export($string, true) . "\n";

// Paso 2: Eliminar OR '1'='1
$string = str_ireplace("OR '1'='1", '', $string);
echo "Después de \"OR '1'='1\": " . var_export($string, true) . "\n";

// Paso 3: Limpiar espacios
$string = preg_replace('/\s+/', ' ', $string);
echo "Después de limpiar espacios: " . var_export($string, true) . "\n";

// Paso 4: Trim
$string = trim($string);
echo "Después de trim: " . var_export($string, true) . "\n";

echo "\n";
echo "🔍 ANÁLISIS:\n";
echo "Esperado longitud: " . strlen($expected) . " caracteres\n";
echo "Actual longitud: " . strlen($string) . " caracteres\n";
echo "¿Son iguales? " . ($string === $expected ? "✅ SÍ" : "❌ NO") . "\n";

if ($string !== $expected) {
    echo "\n📊 DIFERENCIA:\n";
    echo "Esperado bytes: " . bin2hex($expected) . "\n";
    echo "Actual bytes: " . bin2hex($string) . "\n";
}

?>