<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\DynamicColumn;

use Oct8pus\StatsTable\StatsTableBuilder;

class CallbackColumnBuilder implements DynamicColumnBuilderInterface
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable) : array
    {
        $values = [];

        foreach ($statsTable->getIndexes() as $index) {
            // recreate line
            $line = [];

            foreach ($statsTable->getColumns() as $columnName => $column) {
                $columnValues = $column->getValues();
                $line = array_merge($line, [$columnName => $columnValues[$index]]);
            }

            $values[$index] = call_user_func_array($this->callback, [$line]);
        }

        return $values;
    }
}
