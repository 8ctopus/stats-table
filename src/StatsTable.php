<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

use Oct8pus\StatsTable\Dumper\Format;

class StatsTable
{
    private array $data;
    private array $headers;
    private array $dataFormats;
    private array $aggregations;
    private array $aggregationsFormats;
    private array $metaData;

    /**
     * Constructs a new stats table
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
     * Remove column
     *
     * @param string $columnName
     *
     * @return self
     */
    public function removeColumn(string $columnName) : self
    {
        return $this->removeColumns([$columnName]);
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
     * Sort by one column
     *
     * @param string $columnName
     * @param bool   $asc
     *
     * @return self
     */
    public function sortColumn(string $columnName, bool $asc = true) : self
    {
        $this->sortMultipleColumn([$columnName => $asc]);
        return $this;
    }

    /**
     * Sort by one column with a custom compare function
     *
     * @param string   $columnName
     * @param callable $compareFunc custom compare function that must return 0, -1 or 1
     *
     * @return self
     */
    public function uSortColumn(string $columnName, callable $compareFunc) : self
    {
        $this->uSortMultipleColumn([$columnName => $compareFunc]);
        return $this;
    }

    /**
     * Sort by multiple columns
     *
     * @param array $columns Associative array : KEY => column name, VALUE => Sort direction (boolean)
     *
     * @return self
     */
    public function sortMultipleColumn(array $columns) : self
    {
        $compareFuncList = [];

        foreach ($columns as $colName => $asc) {
            $columnFormat = array_key_exists($colName, $this->dataFormats) ? $this->dataFormats[$colName] : Format::STRING;
            $compareFuncList[$colName] = $this->getCompareFunction($columnFormat, $asc);
        }

        $this->uSortMultipleColumn($compareFuncList);
        return $this;
    }

    /**
     * Sort by multiple columns with a custom compare function
     *
     * @param array $columns Associative array : KEY => column name, VALUE => Custom function (function)
     *
     * @return self
     */
    public function uSortMultipleColumn(array $columns) : self
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

    /**
     * Remove columns for a line
     *
     * @param array $line       Referenced line to filter
     * @param array $columnsMap An array indexed by columns to exclude. Value doesn't matter.
     *
     * @return void
     */
    protected function removeColumnsInLine(array &$line, array $columnsMap) : void
    {
        foreach (array_keys($line) as $k) {
            if (array_key_exists($k, $columnsMap)) {
                unset($line[$k]);
            }
        }
    }

    private function getCompareFunction($format, $asc) : mixed
    {
        $genericFunc = static function ($a, $b) use ($asc) {
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? ($asc ? -1 : 1) : ($asc ? 1 : -1);
        };

        $stringCmp = static function ($a, $b) use ($asc) {
            $tmp = strcmp($a, $b);
            return $asc ? $tmp : -$tmp;
        };

        if (Format::STRING === $format) {
            return $stringCmp;
        } else {
            return $genericFunc;
        }
    }
}
