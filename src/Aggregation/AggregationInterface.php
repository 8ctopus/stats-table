<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

interface AggregationInterface
{
    public function aggregate(StatsTableBuilder $statsTable) : mixed;

    public function getFormat() : Format;
}
