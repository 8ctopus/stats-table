<?php

declare(strict_types=1);

namespace Tests\Aggregation;

use Oct8pus\StatsTable\Aggregation\CallbackAggregation;
use Oct8pus\StatsTable\StatsTableBuilder;

class CallbackAggregationTest extends AggregationTestAbstract
{
    public function testAggregation() : void
    {
        $statsTable = $this->getSampleTable();

        $hitsSum = new CallbackAggregation(static function (StatsTableBuilder $builder) : float {
            $values = $builder->getColumn('hits')->getValues();

            return array_sum($values);
        });

        self::assertSame(40.0, $hitsSum->aggregate($statsTable));
    }
}
