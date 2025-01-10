<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

class SumAggregation implements AggregationInterface
{
    private readonly string $columnName;
    private readonly Format $format;

    public function __construct(string $columnName, Format $format = Format::Integer)
    {
        $this->columnName = $columnName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : float
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();

        return array_sum($column);
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
