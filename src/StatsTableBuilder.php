<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

use InvalidArgumentException;
use Oct8pus\StatsTable\Aggregation\AggregationInterface;
use Oct8pus\StatsTable\Aggregation\StaticAggregation;
use Oct8pus\StatsTable\DynamicColumn\DynamicColumnBuilderInterface;

class StatsTableBuilder
{
    private array $columns;
    private readonly ?array $indexes;
    private array $defaultValues;

    /**
     * @param array  $table
     * @param array  $headers
     * @param array  $formats
     * @param array  $aggregations
     * @param array  $columnNames
     * @param array  $defaultValues
     * @param ?array $indexes
     * @param array  $metaData
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $table, array $headers = [], array $formats = [], array $aggregations = [], array $columnNames = [], array $defaultValues = [], ?array $indexes = null, array $metaData = [])
    {
        $this->columns = [];

        if (null !== $indexes) {
            $this->indexes = $indexes;
        } else {
            $this->indexes = array_keys($table);
        }

        $this->defaultValues = $defaultValues;

        $this->appendTable($table, $headers, $formats, $aggregations, $columnNames, $defaultValues, $metaData);
    }

    /**
     * Get indexes
     *
     * @return ?array
     */
    public function getIndexes() : ?array
    {
        return $this->indexes;
    }

    /**
     * Add index of data as a new column
     *
     * @param string                $columnName
     * @param ?string               $headerName
     * @param ?Format               $format
     * @param ?AggregationInterface $aggregation
     * @param array                 $metaData
     *
     * @return self
     */
    public function addIndexesAsColumn(string $columnName, ?string $headerName = null, ?Format $format = null, ?AggregationInterface $aggregation = null, array $metaData = []) : self
    {
        $values = [];
        foreach ($this->indexes as $index) {
            $values[$index] = $index;
        }

        $column = new StatsColumnBuilder(
            $values,
            $headerName,
            $format,
            $aggregation,
            $metaData
        );

        $columns = array_reverse($this->columns);
        $columns[$columnName] = $column;
        $this->columns = $columns;

        return $this;
    }

    /**
     * Append columns
     *
     * @param array                  $table
     * @param string[]               $headers
     * @param Format[]               $formats
     * @param AggregationInterface[] $aggregations
     * @param string[]               $columnNames
     * @param array[]                $defaultValues
     * @param array                  $metaData
     *
     * @return self
     */
    public function appendTable(array $table, array $headers, array $formats, array $aggregations, array $columnNames = [], array $defaultValues = [], array $metaData = []) : self
    {
        $this->defaultValues = array_merge($this->defaultValues, $defaultValues);

        if (count($columnNames) === 0 && count($table) !== 0) {
            $columnNames = array_keys(reset($table));
        }

        if (count($columnNames) === 0 && count($headers) !== 0) {
            $columnNames = array_keys($headers);
        }

        foreach ($columnNames as $columnName) {
            $column = new StatsColumnBuilder(
                $this->getAssocColumn($table, $columnName),
                $this->getParameter($headers, $columnName, $columnName),
                $this->getParameter($formats, $columnName),
                $this->getParameter($aggregations, $columnName),
                $this->getParameter($metaData, $columnName, [])
            );

            if (count($this->defaultValues)) {
                $column->insureIsFilled($this->indexes, $this->defaultValues[$columnName]);
            }

            $this->columns[$columnName] = $column;
        }

        return $this;
    }

    /**
     * Returns an associative table only with selected column
     * Fill with default value if column not in a row
     *
     * @param array  $table
     * @param string $columnName
     * @param mixed  $defaultValue
     *
     * @return array The column
     */
    public function getAssocColumn(array $table, string $columnName, mixed $defaultValue = null) : array
    {
        $values = [];

        foreach ($table as $key => $line) {
            if (array_key_exists($columnName, $line)) {
                $values[$key] = $line[$columnName];
            } else {
                $values[$key] = $defaultValue;
            }
        }

        return $values;
    }

    /**
     * Get column
     *
     * @param string $column
     *
     * @return StatsColumnBuilder
     *
     * @throws InvalidArgumentException
     */
    public function getColumn(string $column) : StatsColumnBuilder
    {
        if (!array_key_exists($column, $this->columns)) {
            throw new InvalidArgumentException("Unable to find column {$column} in columns - " . implode(',', array_keys($this->columns)));
        }

        return $this->columns[$column];
    }

    /**
     * Get column names
     *
     * @return string[]
     */
    public function getColumnNames() : array
    {
        return array_keys($this->columns);
    }

    /**
     * Add dynamic column
     *
     * @param string                        $columnName
     * @param DynamicColumnBuilderInterface $dynamicColumn
     * @param string                        $header
     * @param Format                        $format
     * @param ?AggregationInterface         $aggregation
     * @param array                         $metaData
     *
     * @return self
     */
    public function addDynamicColumn(string $columnName, DynamicColumnBuilderInterface $dynamicColumn, string $header = '', ?Format $format = null, ?AggregationInterface $aggregation = null, array $metaData = []) : self
    {
        $values = $dynamicColumn->buildColumnValues($this);
        $this->columns[$columnName] = new StatsColumnBuilder($values, $header, $format, $aggregation, $metaData);

        return $this;
    }

    /**
     * Add column
     *
     * @param string                $column
     * @param array                 $values
     * @param string                $header
     * @param ?Format               $format
     * @param ?AggregationInterface $aggregation
     * @param array                 $metaData
     *
     * @return self
     */
    public function addColumn(string $column, array $values, string $header = '', ?Format $format = null, ?AggregationInterface $aggregation = null, array $metaData = []) : self
    {
        $this->columns[$column] = new StatsColumnBuilder($values, $header, $format, $aggregation, $metaData);

        return $this;
    }

    /**
     * Build table
     *
     * @param array $columns desired columns
     *
     * @return StatsTable
     */
    public function build(array $columns = []) : StatsTable
    {
        $data = [];

        foreach ($this->indexes as $index) {
            $columnsNames = array_keys($this->columns);

            $line = [];

            foreach ($columnsNames as $columnName) {
                $columnValues = $this->columns[$columnName]->getValues();

                $line = array_merge($line, [$columnName => $columnValues[$index]]);
            }

            $data[$index] = $this->orderColumns($line, $columns);
        }

        $headers = [];
        $dataFormats = [];
        $aggregations = [];
        $aggregationsFormats = [];
        $metaData = [];

        foreach ($this->columns as $columnName => $column) {
            $dataFormats[$columnName] = $column->getFormat();

            $headers = array_merge($headers, [$columnName => $column->getHeaderName()]);
            $metaData = array_merge($metaData, [$columnName => $column->getMetaData()]);

            $columnAggregation = $column->getAggregation();

            if ($columnAggregation) {
                $aggregationValue = $columnAggregation->aggregate($this);
                $aggregationsFormats[$columnName] = $columnAggregation->getFormat();
            } else {
                $aggregationValue = null;
            }

            $aggregations = array_merge($aggregations, [$columnName => $aggregationValue]);
        }

        if (array_all($aggregations, function ($value) : bool {
            return $value === null;
        })) {
            $aggregations = [];
        }

        $headers = $this->orderColumns($headers, $columns);
        $metaData = $this->orderColumns($metaData, $columns);
        $aggregations = $this->orderColumns($aggregations, $columns);

        return new StatsTable($data, $headers, $aggregations, $dataFormats, $aggregationsFormats, $metaData);
    }

    /**
     * Order table columns given columns table
     *
     * @param array $table
     * @param array $columns
     *
     * @return array
     */
    public static function orderColumns(array $table, array $columns) : array
    {
        if (!$columns) {
            return $table;
        }

        $result = [];

        foreach ($columns as $column) {
            if (array_key_exists($column, $table)) {
                $result[$column] = $table[$column];
            }
        }

        return $result;
    }

    /**
     * @return StatsColumnBuilder[]
     */
    public function getColumns() : array
    {
        return $this->columns;
    }

    /**
     * Do a groupBy on columns, using aggregations to aggregate data per line
     *
     * @param array|string $columns        Columns to aggregate
     * @param array        $excludeColumns Irrelevant columns to exclude
     *
     * @return self
     */
    public function groupBy(array|string $columns, array $excludeColumns = []) : self
    {
        $groupedData = [];
        $statsTable = $this->build();

        foreach ($statsTable->getData() as $line) {
            $key = implode(
                '-_##_-',
                array_map(
                    static function ($c) use ($line) {
                        return $line[$c];
                    },
                    $columns
                )
            );

            $groupedData[$key][] = $line;
        }

        $filterLine = static function ($line) use ($excludeColumns) {
            foreach ($excludeColumns as $c) {
                unset($line[$c]);
            }

            return $line;
        };

        $headers = $filterLine(
            array_map(
                static function (StatsColumnBuilder $c) : string {
                    return $c->getHeaderName();
                },
                $this->columns
            )
        );

        $formats = $filterLine(
            array_map(
                static function (StatsColumnBuilder $c) : Format {
                    return $c->getFormat();
                },
                $this->columns
            )
        );

        $aggregations = $filterLine(
            array_map(
                static function (StatsColumnBuilder $c) : ?AggregationInterface {
                    return $c->getAggregation();
                },
                $this->columns
            )
        );

        $metaData = $filterLine(
            array_map(
                static function (StatsColumnBuilder $c) : array {
                    return $c->getMetaData();
                },
                $this->columns
            )
        );

        $data = [];

        foreach ($groupedData as $lines) {
            $tmpAggregations = $aggregations;

            // add static aggregation for group by fields
            foreach ($columns as $column) {
                $oneLine = current($lines);
                $value = $oneLine[$column];
                $tmpAggregations[$column] = new StaticAggregation($value, Format::String);
            }

            $tmpTableBuilder = new self(
                array_map($filterLine, $lines),
                $headers,
                $formats,
                $tmpAggregations,
                [],
                [],
                null,
                $metaData
            );

            $tmpTable = $tmpTableBuilder->build();
            $data[] = $tmpTable->getAggregations();
        }

        return new self(
            $data,
            $headers,
            $formats,
            $aggregations,
            [],
            [],
            null,
            $metaData
        );
    }

    /**
     * Get an indexed value in a table. Same as ParameterBag
     *
     * @param array  $values
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    private function getParameter(array $values, string $key, mixed $defaultValue = null) : mixed
    {
        return array_key_exists($key, $values) ? $values[$key] : $defaultValue;
    }
}
