<?php

declare(strict_types=1);

namespace Tests\DynamicColumn;

use Oct8pus\StatsTable\DynamicColumn\RelativeColumnBuilder;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

class RelativeColumnBuilderTest extends TestCase
{
    public function testWithData() : void
    {
        $statsTable = new StatsTableBuilder(
            [
                'first' => ['a' => 1, 'b' => 2, 'c' => 0],
                'second' => ['a' => 4, 'b' => 5, 'd' => 0],
            ]
        );

        $aColumnBuilder = new RelativeColumnBuilder('a');
        self::assertEquals(
            ['first' => .2, 'second' => .8],
            $aColumnBuilder->buildColumnValues($statsTable)
        );

        $abColumnBuilder = new RelativeColumnBuilder(['a', 'b']);
        self::assertEquals(
            ['first' => .25, 'second' => .75],
            $abColumnBuilder->buildColumnValues($statsTable)
        );

        $cColumnBuilder = new RelativeColumnBuilder('c');
        self::assertEquals(
            ['first' => 0, 'second' => 0],
            $cColumnBuilder->buildColumnValues($statsTable)
        );
    }
}
