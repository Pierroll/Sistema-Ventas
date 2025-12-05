<?php

declare(strict_types=1);

// Asegura que la clase se cargue (por si Composer falla)
require_once __DIR__ . '/../../Config/App/Controller.php';
require_once __DIR__ . '/../../Config/App/Views.php';

use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists('Controller'));
    }

    public function testViewsClassExists(): void
    {
        $this->assertTrue(class_exists('Views'));
    }

    public function testControllerCanBeInstantiated(): void
    {
        // Usar \Controller garantiza que se refiera a la clase global, no a Tests\Unit\Controller
        $controller = new class extends \Controller {
            public function testMethod() {
                return 'test';
            }
        };

        $this->assertInstanceOf(\Controller::class, $controller);
        $this->assertEquals('test', $controller->testMethod());
    }
}
