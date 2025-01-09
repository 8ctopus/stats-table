<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\RatioAggregation;
use IgraalOSL\StatsTable\Dumper\Format;

class RatioAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $format = Format::FLOAT2;
        $subscribersRatio = new RatioAggregation('hits', 'subscribers', $format);
        self::assertSame(13/40, $subscribersRatio->aggregate($statsTable));
        self::assertSame($format, $subscribersRatio->getFormat());
    }
}
