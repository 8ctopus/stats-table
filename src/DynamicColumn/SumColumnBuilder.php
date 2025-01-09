<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\DynamicColumn;

use Oct8pus\StatsTable\StatsTableBuilder;

class SumColumnBuilder implements DynamicColumnBuilderInterface
{
    protected $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable) : array
    {
        $column = [];

        $columnsValues = array_map(
            static function ($columnName) use ($statsTable) {
                return $statsTable->getColumn($columnName)->getValues();
            },
            $this->columns
        );

        foreach ($statsTable->getIndexes() as $index) {
            $lineValues = array_map(
                static function ($array) use ($index) {
                    return $array[$index];
                },
                $columnsValues
            );

            $column[$index] = array_sum($lineValues);
        }

        return $column;
    }
}
