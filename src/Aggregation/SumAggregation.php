<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Format;
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
        $values = $statsTable->getColumn($this->columnName)?->getValues();

        if ($values === null) {
            return 0;
        }

        return array_sum($values);
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
