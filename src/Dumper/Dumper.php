<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;

abstract class Dumper implements DumperInterface
{
    protected $enableHeaders = true;
    protected $enableAggregation = true;

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
     * @param mixed $value
     *
     * @return mixed
     */
    protected function formatValue(Format $format, mixed $value) : mixed
    {
        switch ($format) {
            case Format::fDate:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                break;

            case Format::fDateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d H:i:s');
                }
                break;

            case Format::fFloat:
                return sprintf('%.2f', $value);

            case Format::fInteger:
                return sprintf('%d', $value);

            case Format::fPercent:
                return $this->formatValue(Format::fInteger, $value) . ' %';

            case Format::fPercent2:
                return $this->formatValue(Format::fFloat, $value) . ' %';

            case Format::fMoney:
                return $this->formatValue(Format::fInteger, $value) . ' €';

            case Format::fMoney2:
                return $this->formatValue(Format::fFloat, $value) . ' €';
        }

        return $value;
    }
}
