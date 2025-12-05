<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    /**
     * Test strClean function with various inputs
     */
    public function testStrCleanBasicInput(): void
    {
        $input = "  Hello World  ";
        $expected = "Hello World";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }

    public function testStrCleanWithScriptTags(): void
    {
        $input = "Hello <script>alert('xss')</script> World";
        $expected = "Hello alert('xss') World";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }

    public function testStrCleanWithSQLInjection(): void
    {
        $input = "SELECT * FROM users WHERE id = 1 OR '1'='1'";
        $expected = "users WHERE id = 1 OR ";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }

    public function testStrCleanWithMultipleSpaces(): void
    {
        $input = "Hello    World   Test";
        $expected = "Hello World Test";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }

    public function testStrCleanWithSpecialCharacters(): void
    {
        $input = "Hello^[World]--Test";
        $expected = "HelloWorldTest";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }

    public function testStrCleanEmptyString(): void
    {
        $input = "";
        $expected = "";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }

    public function testStrCleanNullInput(): void
    {
        $input = null;
        $result = strClean($input);
        $this->assertIsString($result);
    }

    public function testStrCleanWithSlash(): void
    {
        $input = "Hello\\World";
        $expected = "HelloWorld";
        $result = strClean($input);
        $this->assertEquals($expected, $result);
    }
}