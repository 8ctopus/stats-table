<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

use Oct8pus\StatsTable\Aggregation\AggregationInterface;

class StatsColumnBuilder
{
    /**
     * @param array                $values      Associative array like index => { name => value }
     * @param string               $headerName  Header name
     * @param ?string               $format      Format
     * @param AggregationInterface|null $aggregation Aggregation
     */
    public function __construct(array $values, string $headerName = '', ?string $format = null, ?AggregationInterface $aggregation = null, $metaData = [])
    {
        $this->values = $values;
        $this->headerName = $headerName;
        $this->format = $format;
        $this->aggregation = $aggregation;
        $this->metaData = $metaData;
    }

    /** @var Array The raw values */
    private Array $values;
    /** @var string The format */
    private ?string $format;
    /** @var AggregationInterface The aggregation rule */
    private ?AggregationInterface $aggregation;
    /** @var string The header name */
    private string $headerName;
    /** @var array Column metadata */
    private array $metaData;

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
     * @param  $headerName
     * @return $this
     */
    public function setHeaderName($headerName)
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
     * @param  AggregationInterface $aggregation
     * @return $this
     */
    public function setAggregation(AggregationInterface $aggregation)
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
     * @param $indexes
     * @param $defaultValue
     */
    public function insureIsFilled($indexes, $defaultValue) : void
    {
        $newValues = [];
        foreach ($indexes as $index) {
            $newValues[$index] = array_key_exists($index, $this->values) ? $this->values[$index] : $defaultValue;
        }
        $this->values = $newValues;
    }
}
