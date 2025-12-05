<?php

function strClean($cadena = "")
{
    // 1. Manejar null
    if ($cadena === null) {
        return "";
    }

    // 2. Convertir a string
    $string = (string)$cadena;

    // 3. Trim inicial
    $string = trim($string);

    // 4. Remover espacios múltiples INTERIORES (pero no los de los extremos aún)
    $string = preg_replace('/\s+/', ' ', $string);

    // 5. Remover slashes
    $string = stripslashes($string);

    // 6. Remover scripts
    $string = str_ireplace('<script>', '', $string);
    $string = str_ireplace('</script>', '', $string);
    $string = str_ireplace('<script type=>', '', $string);
    $string = str_ireplace('<script src>', '', $string);
    $string = str_ireplace('javascript:', '', $string);

    // 7. Remover comandos SQL peligrosos
    $string = str_ireplace('SELECT * FROM', '', $string);
    $string = str_ireplace('DELETE FROM', '', $string);
    $string = str_ireplace('INSERT INTO', '', $string);
    $string = str_ireplace('SELECT COUNT(*) FROM', '', $string);
    $string = str_ireplace('DROP TABLE', '', $string);
    $string = str_ireplace('UNION SELECT', '', $string);

    // 8. Remover patrones de SQL injection (solo el patrón peligroso, no el operador)
    $string = str_ireplace("'1'='1'", '', $string);
    $string = str_ireplace("'1'=1", '', $string);
    $string = str_ireplace("'1' = '1'", '', $string);
    $string = str_ireplace("´1´=´1", '', $string);
    $string = str_ireplace('IS NULL', '', $string);
    $string = str_ireplace('LIKE "', '', $string);
    $string = str_ireplace("LIKE '", '', $string);
    $string = str_ireplace('LIKE ´', '', $string);
    $string = str_ireplace('"a"="a', '', $string);
    $string = str_ireplace("'a'='a", '', $string);
    $string = str_ireplace('´a´=´a', '', $string);

    // 9. Remover caracteres especiales problemáticos
    $string = str_ireplace('--', '', $string);
    $string = str_ireplace(';', '', $string);
    $string = str_ireplace('^', '', $string);
    $string = str_ireplace('[', '', $string);
    $string = str_ireplace(']', '', $string);
    $string = str_ireplace('\\', '', $string);
    $string = str_ireplace('==', '', $string);

    // 10. Limpiar espacios múltiples nuevamente
    $string = preg_replace('/\s+/', ' ', $string);

    // 11. Trim solo al inicio, NO al final (preservar espacios finales si los hay)
    $string = ltrim($string);

    return $string;
}

?>
