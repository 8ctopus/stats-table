<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use Oct8pus\StatsTable\Aggregation\RatioAggregation;
use Oct8pus\StatsTable\Format;

class RatioAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $subscribersRatio = new RatioAggregation('hits', 'subscribers', Format::Float);

        self::assertSame(13 / 40, $subscribersRatio->aggregate($statsTable));
        self::assertSame(Format::Float, $subscribersRatio->getFormat());
    }
}
