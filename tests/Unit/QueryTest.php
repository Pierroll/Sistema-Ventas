<?php

declare(strict_types=1);


use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testQueryClassExists(): void
    {
        $this->assertTrue(class_exists('Query'));
    }

    public function testConexionClassExists(): void
    {
        $this->assertTrue(class_exists('Conexion'));
    }

    public function testQueryCanBeInstantiated(): void
    {
        $query = new class extends Query {
            public function testMethod() {
                return 'test';
            }
        };

        $this->assertInstanceOf(Query::class, $query);
        $this->assertEquals('test', $query->testMethod());
    }
}