<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

/**
 * Returns a fixed value. Useful for first column
 */
class StaticAggregation implements AggregationInterface
{
    private $value;
    private string $format;

    public function __construct($value, string $format = Format::STRING)
    {
        $this->value = $value;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : mixed
    {
        return $this->value;
    }

    public function getFormat() : string
    {
        return $this->format;
    }
}
