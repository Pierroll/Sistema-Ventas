<?php
declare(strict_types=1);

// Config/App/Exceptions/ViewNotFoundException.php

namespace App\Config\Exceptions;

use RuntimeException;

/**
 * Se lanza cuando una vista/clase de vista no puede ser localizada/cargada.
 */
final class ViewNotFoundException extends RuntimeException
{
    public function __construct(string $target, string $message = 'Vista no encontrada', int $code = 404)
    {
        // $target puede ser ruta de archivo ó FQCN de la vista
        parent::__construct($message . ': ' . $target, $code);
    }
}
