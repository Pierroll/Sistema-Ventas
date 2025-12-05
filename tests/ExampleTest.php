<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testSumaBasica(): void
    {
        $this->assertSame(4, 2 + 2);
    }
}
