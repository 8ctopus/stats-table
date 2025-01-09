<?php

declare(strict_types=1);

namespace Tests\DynamicColumn;

use Oct8pus\StatsTable\DynamicColumn\SumColumnBuilder;
use Oct8pus\StatsTable\StatsColumnBuilder;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

class SumColumnBuilderTest extends TestCase
{
    public function test() : void
    {
        $table = [
            '2014-01-01' => ['hits' => 10, 'subscribers' => 5],
            '2014-01-02' => ['hits' => 30, 'subscribers' => 9],
            '2014-01-03' => ['hits' => 0, 'subscribers' => 0],
        ];

        $statsTable = new StatsTableBuilder($table);

        $sumBuilder = new SumColumnBuilder(['subscribers', 'hits']);

        $statsTable->addDynamicColumn('sum', $sumBuilder, 'Sum');

        $sumData = [
            '2014-01-01' => 15,
            '2014-01-02' => 39,
            '2014-01-03' => 0
        ];
        $sumColumn = new StatsColumnBuilder($sumData, 'Sum');
        self::assertEquals($sumColumn, $statsTable->getColumn('sum'));
    }
}
