<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

/**
 * Class SumAggregation
 * Returns the sum of all elements in the column
 */
class SumAggregation implements AggregationInterface
{
    private $columnName;
    private string $format;

    public function __construct($columnName, $format = Format::INTEGER)
    {
        $this->columnName = $columnName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable)
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();

        return array_sum($column);
    }

    /**
     * @return string
     */
    public function getFormat() : string
    {
        return $this->format;
    }
}
