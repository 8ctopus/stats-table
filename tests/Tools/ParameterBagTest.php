<?php

declare(strict_types=1);

namespace Tests\Tools;

use Oct8pus\StatsTable\Tools\ParameterBag;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testConstructorWithArray() : void
    {
        $data = $this->getSampleData();
        $bag = $this->getSampleBag();

        self::assertSame($data, $bag->toArray());
    }

    public function testConstructorWithParameterBag() : void
    {
        $data = $this->getSampleData();
        $bag = $this->getSampleBag();

        $bag2 = new ParameterBag($bag);
        self::assertSame($data, $bag2->toArray());
    }

    public function testHasKey() : void
    {
        $bag = $this->getSampleBag();

        self::assertTrue($bag->has('1'));
        self::assertFalse($bag->has('3'));
    }

    public function testGet() : void
    {
        $bag = $this->getSampleBag();

        self::assertSame('One', $bag->get('1', 'One value'));
        self::assertSame('Three', $bag->get('3', 'Three'));
    }

    private function getSampleData() : array
    {
        return [
            '1' => 'One',
            '2' => 'Two',
        ];
    }

    private function getSampleBag() : ParameterBag
    {
        return new ParameterBag($this->getSampleData());
    }
}
