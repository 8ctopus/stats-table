<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

class StatsTable
{
    private array $data;
    private array $headers;
    private array $dataFormats;
    private array $aggregations;
    private array $aggregationsFormats;
    private readonly array $metaData;

    /**
     * Construct a new stats table
     *
     * @param array $data
     * @param array $headers
     * @param array $aggregations
     * @param array $dataFormats
     * @param array $aggregationsFormats
     * @param array $metaData
     */
    public function __construct(
        array $data,
        array $headers = [],
        array $aggregations = [],
        array $dataFormats = [],
        array $aggregationsFormats = [],
        array $metaData = []
    ) {
        $this->data = $data;
        $this->headers = $headers;
        $this->aggregations = $aggregations;
        $this->dataFormats = $dataFormats;
        $this->aggregationsFormats = $aggregationsFormats;
        $this->metaData = $metaData;
    }

    /**
     * Remove column
     *
     * @param string $column
     *
     * @return self
     */
    public function removeColumn(string $column) : self
    {
        return $this->removeColumns([$column]);
    }

    /**
     * Remove columns
     *
     * @param array $columns
     *
     * @return self
     */
    public function removeColumns(array $columns) : self
    {
        $columnsMap = array_flip($columns);

        $this->removeColumnsInLine($this->headers, $columnsMap);
        $this->removeColumnsInLine($this->aggregations, $columnsMap);
        $this->removeColumnsInLine($this->dataFormats, $columnsMap);
        $this->removeColumnsInLine($this->aggregationsFormats, $columnsMap);

        foreach ($this->data as &$line) {
            $this->removeColumnsInLine($line, $columnsMap);
        }

        return $this;
    }

    /**
     * Sort by column
     *
     * @param string    $column
     * @param Direction $direction
     *
     * @return self
     */
    public function sortByColumn(string $column, Direction $direction) : self
    {
        $this->sortByColumns([$column => $direction]);
        return $this;
    }

    /**
     * Sort by one column with a custom compare function
     *
     * @param string   $column
     * @param callable $function
     *
     * @return self
     */
    public function uSortByColumn(string $column, callable $function) : self
    {
        $this->uSortByColumns([$column => $function]);
        return $this;
    }

    /**
     * Sort by columns
     *
     * @param array $columns Associative array : KEY => column name, VALUE => Sort direction
     *
     * @return self
     */
    public function sortByColumns(array $columns) : self
    {
        $compareFuncList = [];

        foreach ($columns as $colName => $direction) {
            $columnFormat = array_key_exists($colName, $this->dataFormats) ? $this->dataFormats[$colName] : Format::String;
            $compareFuncList[$colName] = $this->getCompareFunction($columnFormat, $direction);
        }

        $this->uSortByColumns($compareFuncList);
        return $this;
    }

    /**
     * Sort by columns with a custom compare function
     *
     * @param array $columns Associative array : KEY => column name, VALUE => Custom function
     *
     * @return self
     */
    public function uSortByColumns(array $columns) : self
    {
        $sort = static function (mixed $a, mixed $b) use ($columns) : int {
            foreach ($columns as $colName => $fn) {
                $tmp = $fn($a[$colName], $b[$colName]);

                if ($tmp !== 0) {
                    return $tmp;
                }
            }

            return 0;
        };

        uasort($this->data, $sort);
        return $this;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getAggregations() : array
    {
        return $this->aggregations;
    }

    public function getAggregationsFormats() : array
    {
        return $this->aggregationsFormats;
    }

    public function getDataFormats() : array
    {
        return $this->dataFormats;
    }

    public function getMetaData() : array
    {
        return $this->metaData;
    }

    /**
     * Remove columns from line
     *
     * @param array $line    Referenced line to filter
     * @param array $columns An array indexed by columns to exclude. Value doesn't matter.
     *
     * @return void
     */
    protected function removeColumnsInLine(array &$line, array $columns) : void
    {
        foreach (array_keys($line) as $k) {
            if (array_key_exists($k, $columns)) {
                unset($line[$k]);
            }
        }
    }

    /**
     * Get compare function
     *
     * @param Format    $format
     * @param Direction $direction
     *
     * @return callable
     */
    private function getCompareFunction(Format $format, Direction $direction) : callable
    {
        if (Format::String === $format) {
            return static function (string $a, string $b) use ($direction) {
                $tmp = strcmp($a, $b);
                return $direction === Direction::Ascending ? $tmp : -$tmp;
            };
        }

        return static function (mixed $a, mixed $b) use ($direction) : int {
            if ($a === $b) {
                return 0;
            }

            return ($a < $b) ? ($direction === Direction::Ascending ? -1 : 1) : ($direction === Direction::Ascending ? 1 : -1);
        };
    }
}
