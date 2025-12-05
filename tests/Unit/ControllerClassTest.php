<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ControllerClassTest extends TestCase
{
    public function testVentasControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Ventas'));
    }

    public function testProductosControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Productos'));
    }

    public function testUsuariosControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Usuarios'));
    }

    public function testClientesControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Clientes'));
    }

    public function testHomeControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Home'));
    }

    public function testAdministracionControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Administracion'));
    }

    public function testCotizacionesControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Cotizaciones'));
    }

    public function testLandingControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Landing'));
    }

    public function testCajasControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Cajas'));
    }

    public function testComprasControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Compras'));
    }

    public function testCreditosControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Creditos'));
    }

    public function testApartadosControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Apartados'));
    }

    public function testErrorsControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Errors'));
    }
}
