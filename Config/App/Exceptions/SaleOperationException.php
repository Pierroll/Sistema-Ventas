<?php
declare(strict_types=1);

// Config/App/Exceptions/SaleOperationException.php

namespace App\Config\Exceptions;

use RuntimeException;

/**
 * Error de negocio durante una operación de venta.
 */
final class SaleOperationException extends RuntimeException
{
    public function __construct(string $message = 'Error en operación de venta', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
