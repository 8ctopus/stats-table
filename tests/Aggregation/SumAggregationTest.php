<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use Oct8pus\StatsTable\Aggregation\SumAggregation;

class SumAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $hitsSum = new SumAggregation('hits');
        self::assertSame(40.0, $hitsSum->aggregate($statsTable));
    }
}
