<?php

declare(strict_types=1);

namespace Tests\DynamicColumn;

use Oct8pus\StatsTable\DynamicColumn\RatioColumnBuilder;
use Oct8pus\StatsTable\StatsColumnBuilder;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

class RatioColumnBuilderTest extends TestCase
{
    public function testBuilder() : void
    {
        $table = [
            '2014-01-01' => ['hits' => 10, 'subscribers' => 5],
            '2014-01-02' => ['hits' => 30, 'subscribers' => 9],
            '2014-01-03' => ['hits' => 0, 'subscribers' => 0]
        ];

        $statsTable = new StatsTableBuilder($table);

        $ratioBuilder = new RatioColumnBuilder('subscribers', 'hits', 'N/A');

        $statsTable->addDynamicColumn('ratio', $ratioBuilder, 'Ratio');

        $ratioData = [
            '2014-01-01' => .5,
            '2014-01-02' => .3,
            '2014-01-03' => 'N/A'
        ];
        $ratioColumn = new StatsColumnBuilder($ratioData, 'Ratio');
        self::assertEquals($ratioColumn, $statsTable->getColumn('ratio'));
    }
}
