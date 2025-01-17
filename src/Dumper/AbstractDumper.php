<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\Tools\ParameterBag;

abstract class AbstractDumper implements DumperInterface
{
    protected ParameterBag $options;
    protected bool $enableHeaders = true;
    protected bool $enableAggregation = true;

    public function __construct(array $options = [])
    {
        $this->options = new ParameterBag($options);
    }

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
     * Format value
     *
     * @param ?Format $format
     * @param mixed   $value
     *
     * @return null|float|int|string
     */
    protected function formatValue(?Format $format, mixed $value) : null|float|int|string
    {
        $decimals = $this->options->get('decimals_count');
        $decimalSep = $this->options->get('decimals_separator');
        $thousandsSep = $this->options->get('thousands_separator');

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

            case Format::Integer:
                return number_format((float) $value, 0, $decimalSep, $thousandsSep);

            case Format::Float:
                return number_format((float) $value, $decimals, $decimalSep, $thousandsSep);

            case Format::Percent:
                return $this->formatValue(Format::Integer, $value * 100) . '%';

            case Format::Percent2:
                return $this->formatValue(Format::Float, $value * 100) . '%';

            case Format::Money:
                return $this->formatValue(Format::Integer, $value) . ' €';

            case Format::Money2:
                return $this->formatValue(Format::Float, $value) . ' €';

            case Format::String:
            default:
                return $value;
        }

        return $value;
    }
}
