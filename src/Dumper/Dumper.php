<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;

abstract class Dumper implements DumperInterface
{
    protected bool $enableHeaders = true;
    protected bool $enableAggregation = true;

    /**
     * Enable headers
     *
     * @param bool $enableHeaders
     */
    public function enableHeaders(bool $enableHeaders = true) : void
    {
        $this->enableHeaders = $enableHeaders;
    }

    /**
     * Enable aggregation
     *
     * @param bool $enableAggregation
     */
    public function enableAggregation(bool $enableAggregation = true) : void
    {
        $this->enableAggregation = $enableAggregation;
    }

    /**
     * Default value formatter
     *
     * @param Format $format
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function formatValue(Format $format, mixed $value) : mixed
    {
        switch ($format) {
            case Format::Date:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                break;

            case Format::DateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d H:i:s');
                }
                break;

            case Format::Float:
                return sprintf('%.2f', $value);

            case Format::Integer:
                return sprintf('%d', $value);

            case Format::Percent:
                return $this->formatValue(Format::Integer, $value) . ' %';

            case Format::Percent2:
                return $this->formatValue(Format::Float, $value) . ' %';

            case Format::Money:
                return $this->formatValue(Format::Integer, $value) . ' €';

            case Format::Money2:
                return $this->formatValue(Format::Float, $value) . ' €';
        }

        return $value;
    }
}
