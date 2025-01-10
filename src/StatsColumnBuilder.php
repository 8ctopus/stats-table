<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

use Oct8pus\StatsTable\Aggregation\AggregationInterface;
use Oct8pus\StatsTable\Dumper\Format;

class StatsColumnBuilder
{
    private array $values;
    private readonly ?Format $format;
    private ?AggregationInterface $aggregation;
    private string $headerName;
    private readonly array $metaData;

    /**
     * @param array                 $values      Associative array like index => { name => value }
     * @param string                $headerName
     * @param ?Format               $format
     * @param ?AggregationInterface $aggregation
     * @param array                 $metaData
     */
    public function __construct(array $values, string $headerName = '', ?Format $format = null, ?AggregationInterface $aggregation = null, array $metaData = [])
    {
        $this->values = $values;
        $this->headerName = $headerName;
        $this->format = $format;
        $this->aggregation = $aggregation;
        $this->metaData = $metaData;
    }

    public function getValues() : array
    {
        return $this->values;
    }

    public function getHeaderName() : string
    {
        return $this->headerName;
    }

    public function setHeaderName(string $headerName) : self
    {
        $this->headerName = $headerName;

        return $this;
    }

    public function getAggregation() : ?AggregationInterface
    {
        return $this->aggregation;
    }

    public function setAggregation(AggregationInterface $aggregation) : self
    {
        $this->aggregation = $aggregation;

        return $this;
    }

    public function getFormat() : ?Format
    {
        return $this->format;
    }

    public function getMetaData() : array
    {
        return $this->metaData;
    }

    public function setMetaData(array $metaData) : void
    {
        $this->metaData = $metaData;
    }

    /**
     * Ensure column is filled with given indexes. If not, it will be filled with default values
     *
     * @param array $indexes
     * @param mixed $defaultValue
     *
     * @return void
     */
    public function insureIsFilled(array $indexes, mixed $defaultValue) : void
    {
        $newValues = [];

        foreach ($indexes as $index) {
            $newValues[$index] = array_key_exists($index, $this->values) ? $this->values[$index] : $defaultValue;
        }

        $this->values = $newValues;
    }
}
