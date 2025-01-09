<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

use Oct8pus\StatsTable\Dumper\Format;

class StatsTable
{
    private $headers;
    private $aggregations;
    private $data;
    private $dataFormats;
    private $aggregationsFormats;
    private $metaData;

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getAggregations() : array
    {
        return $this->aggregations;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getAggregationsFormats() : array
    {
        return $this->aggregationsFormats;
    }

    /**
     * @return array
     */
    public function getDataFormats() : array
    {
        return $this->dataFormats;
    }

    /**
     * @return array
     */
    public function getMetaData() : array
    {
        return $this->metaData;
    }

    /**
     * Constructs a new stats table
     *
     * @param array $data
     * @param       $headers
     * @param       $aggregations
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
        $this->headers = $headers;
        $this->data = $data;
        $this->aggregations = $aggregations;
        $this->dataFormats = $dataFormats;
        $this->aggregationsFormats = $aggregationsFormats;
        $this->metaData = $metaData;
    }

    /**
     * Remove a single column in table
     *
     * @param mixed $columnName
     *
     * @return StatsTable
     */
    public function removeColumn(mixed $columnName) : self
    {
        return $this->removeColumns([$columnName]);
    }

    /**
     * Remove columns in table
     *
     * @param array $columns
     *
     * @return StatsTable
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
     * Internal helper to remove columns for a line.
     *
     * @param array $line       Line to filter. Referenced.
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

    /**
     * Sort stats table by one column
     *
     * @param string $columnName Name of column
     * @param bool   $asc        Sort direction : TRUE=>Ascending, FALSE=>Descending
     *
     * @return StatsTable
     */
    public function sortColumn(string $columnName, bool $asc = true) : self
    {
        $this->sortMultipleColumn([$columnName => $asc]);
        return $this;
    }

    /**
     * Sort stats table by one column with a custom compare function
     *
     * @param string   $columnName  Name of column
     * @param function $compareFunc custom compare function that should return 0, -1 or 1
     *
     * @return StatsTable
     */
    public function uSortColumn(string $columnName, $compareFunc) : self
    {
        $this->uSortMultipleColumn([$columnName => $compareFunc]);
        return $this;
    }

    /**
     * Sort stats table by multiple column
     *
     * @param array $columns Associative array : KEY=> column name (string), VALUE=> Sort direction (boolean)
     *
     * @return $this
     */
    public function sortMultipleColumn(array $columns)
    {
        $compareFuncList = [];
        foreach ($columns as $colName => $asc) {
            $columnFormat = array_key_exists($colName, $this->dataFormats) ? $this->dataFormats[$colName] : Format::STRING;
            $compareFuncList[$colName] = $this->_getFunctionForFormat($columnFormat, $asc);
        }

        $this->uSortMultipleColumn($compareFuncList);
        return $this;
    }

    /**
     * Sort stats table by multiple column with a custom compare function
     *
     * @param array $columns Associative array : KEY=> column name (string), VALUE=> Custom function (function)
     *
     * @return $this
     */
    public function uSortMultipleColumn(array $columns)
    {
        $sort = static function ($a, $b) use ($columns) {
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

    private function _getFunctionForFormat($format, $asc)
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
