<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\StaticAggregation;

class StaticAggregationTest extends AggregationTestAbstract
{
    public function testStaticAggregation() : void
    {
        $statsTable = $this->getSampleTable();
        $staticAggregation = new StaticAggregation('value');
        self::assertSame('value', $staticAggregation->aggregate($statsTable));
    }
}
 