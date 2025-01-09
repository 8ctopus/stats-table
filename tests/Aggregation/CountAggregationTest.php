<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use Oct8pus\StatsTable\Aggregation\CountAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;

class CountAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $lineNumber = new CountAggregation('hits');
        self::assertSame(2, $lineNumber->aggregate($statsTable));
    }
}
