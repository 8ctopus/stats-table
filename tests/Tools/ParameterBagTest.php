<?php

declare(strict_types=1);

namespace Tests\Tools;

use Oct8pus\StatsTable\Tools\ParameterBag;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testConstructorWithArray() : void
    {
        $bag = new ParameterBag($this->getSampleData());

        $excepted = [
            'decimals_count' => 2,
            'decimals_separator' => ',',
            'thousands_separator' => ' ',
            'distance' => 'meter',
            'speed' => 'km/h',
        ];

        self::assertSame($excepted, $bag->toArray());
    }

    public function testConstructorWithParameterBag() : void
    {
        $bag = new ParameterBag($this->getSampleData());
        $bag = new ParameterBag($bag);

        $excepted = [
            'decimals_count' => 2,
            'decimals_separator' => ',',
            'thousands_separator' => ' ',
            'distance' => 'meter',
            'speed' => 'km/h',
        ];

        self::assertSame($excepted, $bag->toArray());
    }

    public function testHasKey() : void
    {
        $bag = new ParameterBag($this->getSampleData());

        self::assertTrue($bag->has('distance'));
        self::assertFalse($bag->has('time'));
    }

    public function testGet() : void
    {
        $bag = new ParameterBag($this->getSampleData());

        self::assertSame('meter', $bag->get('distance', 'One value'));
        self::assertSame('Three', $bag->get('3', 'Three'));
    }

    private function getSampleData() : array
    {
        return [
            'distance' => 'meter',
            'speed' => 'km/h',
        ];
    }
}
