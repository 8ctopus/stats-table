<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

/**
 * Class CountAggregation
 * Returns number of element in dataset
 */
class CountAggregation implements AggregationInterface
{
    private string $columnName;
    private string $format;

    public function __construct(string $columnName, string $format = Format::INTEGER)
    {
        $this->columnName = $columnName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : mixed
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();

        return count($column);
    }

    /**
     * @return string
     */
    public function getFormat() : string
    {
        return $this->format;
    }
}
