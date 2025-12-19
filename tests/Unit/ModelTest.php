<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function testVentasModelClassExists(): void
    {
        $this->assertTrue(class_exists('VentasModel'));
    }

    public function testProductosModelClassExists(): void
    {
        $this->assertTrue(class_exists('ProductosModel'));
    }

    public function testUsuariosModelClassExists(): void
    {
        $this->assertTrue(class_exists('UsuariosModel'));
    }

    public function testClientesModelClassExists(): void
    {
        $this->assertTrue(class_exists('ClientesModel'));
    }

    public function testAdministracionModelClassExists(): void
    {
        $this->assertTrue(class_exists('AdministracionModel'));
    }

    public function testCotizacionesModelClassExists(): void
    {
        $this->assertTrue(class_exists('CotizacionesModel'));
    }

    public function testLandingModelClassExists(): void
    {
        $this->assertTrue(class_exists('LandingModel'));
    }

    public function testCajasModelClassExists(): void
    {
        $this->assertTrue(class_exists('CajasModel'));
    }

    public function testCategoriasModelClassExists(): void
    {
        $this->assertTrue(class_exists('CategoriasModel'));
    }

    public function testMedidasModelClassExists(): void
    {
        $this->assertTrue(class_exists('MedidasModel'));
    }

    public function testProveedorModelClassExists(): void
    {
        $this->assertTrue(class_exists('ProveedorModel'));
    }

    public function testComprasModelClassExists(): void
    {
        $this->assertTrue(class_exists('ComprasModel'));
    }

    public function testCreditosModelClassExists(): void
    {
        $this->assertTrue(class_exists('CreditosModel'));
    }

    public function testApartadosModelClassExists(): void
    {
        $this->assertTrue(class_exists('ApartadosModel'));
    }
}
