<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\DynamicColumn;

use Oct8pus\StatsTable\StatsTableBuilder;

class RatioColumnBuilder implements DynamicColumnBuilderInterface
{
    private string $numerator;
    private string $denominator;
    private mixed $defaultValue;

    /**
     * @param string $numerator
     * @param string $denominator
     * @param mixed  $defaultValue default value if denominator is null
     */
    public function __construct(string $numerator, string $denominator, mixed $defaultValue)
    {
        $this->denominator = $denominator;
        $this->numerator = $numerator;
        $this->defaultValue = $defaultValue;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable) : array
    {
        $column = [];
        $values = $statsTable->getColumn($this->numerator)->getValues();
        $overs = $statsTable->getColumn($this->denominator)->getValues();

        foreach ($statsTable->getIndexes() as $index) {
            $value = $values[$index];
            $over = $overs[$index];
            $column[$index] = $over ? $value / $over : $this->defaultValue;
        }

        return $column;
    }
}
