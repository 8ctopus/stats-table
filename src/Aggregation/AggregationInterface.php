<?php

declare(strict_types=1);

namespace IgraalOSL\StatsTable\Aggregation;

use IgraalOSL\StatsTable\StatsTableBuilder;

interface AggregationInterface
{
    public function aggregate(StatsTableBuilder $statsTable);
    public function getFormat();
}
