<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testConstantsAreDefined(): void
    {
        $this->assertTrue(defined('BASE_URL'));
        $this->assertIsString(BASE_URL);
        $this->assertNotEmpty(BASE_URL);
    }

    public function testBaseUrlFormat(): void
    {
        $this->assertStringContainsString('http', BASE_URL);
        $this->assertStringEndsWith('/', BASE_URL);
    }
}

