<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\DynamicColumn;

use Oct8pus\StatsTable\StatsTableBuilder;

/**
 * Builds a column by creating the relative ratio of the line
 *
 * Example :
 * Given the table with the following data
 * a b c
 * - - -
 * 1 2 3
 * 4 5 6
 *
 * The column created by new $this('a') will create the column
 * .2   = 1/(1+4)
 * .8   = 4/(1+4)
 *
 * The column created by new $this(['a', 'b']) will create the column
 * 0.25 = 3/(1+2+4+5)
 * 0.75 = 13/(1+2+4+5)
 */
class RelativeColumnBuilder implements DynamicColumnBuilderInterface
{
    protected array $columns;

    /**
     * Constructor
     *
     * @param array|string $columns Columns to consider
     */
    public function __construct(array|string $columns)
    {
        $this->columns = is_array($columns) ? $columns : [$columns];
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumnValues(StatsTableBuilder $statsTable) : array
    {
        $summedDataColumn = new SumColumnBuilder($this->columns);
        $summedData = $summedDataColumn->buildColumnValues($statsTable);

        $total = array_sum($summedData);

        if ($total) {
            $column = array_map(
                static function ($value) use ($total) {
                    return $value / $total;
                },
                $summedData
            );
        } else {
            $column = array_fill(0, count($summedData), 0);
        }

        return array_combine(
            array_keys($summedData),
            $column
        );
    }
}
