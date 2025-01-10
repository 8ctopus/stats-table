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
    private readonly string $value;
    private readonly Format $format;

    public function __construct(string $value, Format $format = Format::String)
    {
        $this->value = $value;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : string
    {
        return $this->value;
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
