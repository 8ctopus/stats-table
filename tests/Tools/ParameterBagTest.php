<?php

declare(strict_types=1);

namespace Tests\Tools;

use Oct8pus\StatsTable\Tools\ParameterBag;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testConstructorWithArray() : void
    {
        $bag = new ParameterBag([
            'decimals_count' => 3,
            'added' => 1,
        ]);

        $excepted = [
            'decimals_count' => 3,
            'decimals_separator' => '.',
            'thousands_separator' => '\'',
            'added' => 1,
        ];

        self::assertSame($excepted, $bag->toArray());
    }

    public function testConstructorWithParameterBag() : void
    {
        $bag = new ParameterBag(new ParameterBag([
            'decimals_count' => 3,
            'added' => 1,
        ]));

        $excepted = [
            'decimals_count' => 3,
            'decimals_separator' => '.',
            'thousands_separator' => '\'',
            'added' => 1,
        ];

        self::assertSame($excepted, $bag->toArray());
    }

    public function testHas() : void
    {
        $bag = new ParameterBag([
            'decimals_count' => 3,
            'added' => 1,
        ]);

        self::assertTrue($bag->has('decimals_count'));
        self::assertTrue($bag->has('added'));
        self::assertFalse($bag->has('time'));
    }

    public function testGet() : void
    {
        $bag = new ParameterBag();

        self::assertSame(2, $bag->get('decimals_count', 3));
        self::assertSame('Three', $bag->get('3', 'Three'));
    }

    public function testSet() : void
    {
        $bag = new ParameterBag();
        $bag->set('decimals_count', 3);
        $bag->set('added', 1);

        $excepted = [
            'decimals_count' => 3,
            'decimals_separator' => '.',
            'thousands_separator' => '\'',
            'added' => 1,
        ];

        self::assertSame($excepted, $bag->toArray());
    }
}
