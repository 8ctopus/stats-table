<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

abstract class Dumper implements DumperInterface
{
    protected $enableHeaders = true;
    protected $enableAggregation = true;

    /**
     * Enable headers
     * @param bool $enableHeaders
     */
    public function enableHeaders(bool $enableHeaders = true) : void
    {
        $this->enableHeaders = $enableHeaders;
    }

    /**
     * Enable aggregation
     * @param bool $enableAggregation
     */
    public function enableAggregation(bool $enableAggregation = true) : void
    {
        $this->enableAggregation = $enableAggregation;
    }

    /**
     * Default value formatter
     * @param $format
     * @param $value
     * @return string|int|float
     */
    protected function formatValue($format, $value) : string|int|float
    {
        switch ($format) {
            case Format::DATE:
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                break;

            case Format::DATETIME:
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d H:i:s');
                }
                break;

            case Format::FLOAT2:
                return sprintf("%.2f", $value);

            case Format::INTEGER:
                return sprintf("%d", $value);

            case Format::PCT:
                return $this->formatValue(Format::INTEGER, $value)." %";

            case Format::PCT2:
                return $this->formatValue(Format::FLOAT2, $value)." %";

            case Format::MONEY:
                return $this->formatValue(Format::INTEGER, $value)." €";

            case Format::MONEY2:
                return $this->formatValue(Format::FLOAT2, $value)." €";
        }

        return $value;
    }
}
