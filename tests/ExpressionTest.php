<?php

use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    /** @test */
    function it_finds_a_string()
    {
        $regex=Expression::make()->find('www');
        $this->assertTrue((string)$regex,'www');

        $regex = Expression::make()->then('www');
        $this->assertTrue((string)$regex,'www');
    }

    /** @test */
    function it_checks_for_anything()
    {
        $regex=Expression::make()->anything();

        $this->asserTrue($regex->test('foo'));
    }

    /** @test */
    function it_maybe_has_a_value()
    {
        $regex=Expression::make()->maybe('http');

        $this->assertTrue($regex->test('http'));
        $this->assertTrue($regex->test(''));
    }

    /** @test */
    function it_can_chain_method_calls()
    {
        $regex = Expression::make()->find('foo')->maybe('.')->then('laracasts');

        $this->assertTrue($regex->test('www.laracasts'));
        $this->assertFalse($regex->test('wwwXlaracasts'));
    }

    /** @test */
    function it_can_exclude_values()
    {
        $regex = Expression::make()->find('foo')->anything('bar')->then('biz');

        $this->assertTrue($regex->test('foobazbiz'));
        $this->assertFalse($regex->test('foobarbiz'));
    }
}