<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\DynamicColumn;

use Oct8pus\StatsTable\StatsTableBuilder;

class RatioColumnBuilder implements DynamicColumnBuilderInterface
{
    private string $valueInternalName;
    private string $overInternalName;
    private mixed $defaultValue;

    /**
     * @param string $valueInternalName The small value
     * @param string $overInternalName  The big value
     * @param mixed  $defaultValue      Default value if big value is null
     */
    public function __construct(string $valueInternalName, string $overInternalName, mixed $defaultValue)
    {
        $this->overInternalName = $overInternalName;
        $this->valueInternalName = $valueInternalName;
        $this->defaultValue = $defaultValue;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable) : array
    {
        $column = [];
        $values = $statsTable->getColumn($this->valueInternalName)->getValues();
        $overs = $statsTable->getColumn($this->overInternalName)->getValues();

        foreach ($statsTable->getIndexes() as $index) {
            $value = $values[$index];
            $over = $overs[$index];
            $column[$index] = $over ? $value / $over : $this->defaultValue;
        }

        return $column;
    }
}
