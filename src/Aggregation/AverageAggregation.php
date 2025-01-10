<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

class AverageAggregation implements AggregationInterface
{
    private string $columnName;
    private Format $format;

    public function __construct(string $columnName, Format $format = Format::INTEGER)
    {
        $this->columnName = $columnName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : mixed
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();
        $sum = array_sum($column);
        $count = count($column);

        return $count ? $sum / $count : 0;
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
