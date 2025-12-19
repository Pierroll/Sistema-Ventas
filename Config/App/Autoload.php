<?php

/**
 * Autocarga básica para clases sin namespace.
 * Compatible con Controllers/, Models/ y Config/App/
 *
 * NOTA: No usamos namespaces, por lo tanto `require_once` es intencional.
 * @SuppressWarnings(PHPMD.RequireOnce)
 */
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . "/$class.php",                            // Config/App/
        __DIR__ . "/../../Controllers/$class.php",          // Controllers/
        __DIR__ . "/../../Models/$class.php",               // Models/
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            // INTENCIONAL: No usamos namespaces. Autoload manual con require_once.
            require_once $path; // NOSONAR - Arquitectura sin namespaces
            break;
        }
    }
});
