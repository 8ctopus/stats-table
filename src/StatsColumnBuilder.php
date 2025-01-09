<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

use Oct8pus\StatsTable\Aggregation\AggregationInterface;

class StatsColumnBuilder
{
    private array $values;
    private ?string $format;
    private ?AggregationInterface $aggregation;
    private string $headerName;
    private array $metaData;

    /**
     * @param array                     $values      Associative array like index => { name => value }
     * @param string                    $headerName  Header name
     * @param ?string                   $format      Format
     * @param ?AggregationInterface     $aggregation Aggregation
     * @param array                     $metaData
     */
    public function __construct(array $values, string $headerName = '', ?string $format = null, ?AggregationInterface $aggregation = null, array $metaData = [])
    {
        $this->values = $values;
        $this->headerName = $headerName;
        $this->format = $format;
        $this->aggregation = $aggregation;
        $this->metaData = $metaData;
    }

    /**
     * @return mixed[]
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function getHeaderName() : string
    {
        return $this->headerName;
    }

    /**
     * @param $headerName
     *
     * @return self
     */
    public function setHeaderName(string $headerName) : self
    {
        $this->headerName = $headerName;

        return $this;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregation() : ?AggregationInterface
    {
        return $this->aggregation;
    }

    /**
     * @param AggregationInterface $aggregation
     *
     * @return self
     */
    public function setAggregation(AggregationInterface $aggregation) : self
    {
        $this->aggregation = $aggregation;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getFormat() : ?string
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getMetaData() : array
    {
        return $this->metaData;
    }

    /**
     * @param array $metaData
     */
    public function setMetaData(array $metaData) : void
    {
        $this->metaData = $metaData;
    }

    /**
     * Ensure column is filled with given indexes. If not, it will be filled with default values
     *
     * @param $indexes
     * @param $defaultValue
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
