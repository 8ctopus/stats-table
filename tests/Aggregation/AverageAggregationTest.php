<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Dumper\Format;

class AverageAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $format = Format::Float;
        $hitsAverage = new AverageAggregation('hits', $format);
        self::assertSame(20.0, $hitsAverage->aggregate($statsTable));
        self::assertSame($format, $hitsAverage->getFormat());
    }
}
