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
    private $columnName;
    private $format;

    public function __construct($columnName, $format = Format::INTEGER)
    {
        $this->columnName = $columnName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable)
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();

        return count($column);
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }
}
