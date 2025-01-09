<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\AverageAggregation;
use IgraalOSL\StatsTable\Dumper\Format;

class AverageAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $format = Format::FLOAT2;
        $hitsAverage = new AverageAggregation('hits', $format);
        self::assertSame(20, $hitsAverage->aggregate($statsTable));
        self::assertSame($format, $hitsAverage->getFormat());
    }
}
