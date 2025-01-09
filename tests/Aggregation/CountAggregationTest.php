<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\CountAggregation;
use IgraalOSL\StatsTable\Aggregation\SumAggregation;

class CountAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $lineNumber = new CountAggregation('hits');
        self::assertSame(2, $lineNumber->aggregate($statsTable));
    }
}
