<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\Tools\ParameterBag;

class DataDumper extends Dumper
{
    private readonly ParameterBag $options;

    public function __construct(array $options = [])
    {
        $this->options = new ParameterBag($options);
    }

    /**
     * Dump table
     *
     * @param  StatsTable $statsTable
     *
     * @return array
     */
    public function dump(StatsTable $statsTable) : array
    {
        $data = $statsTable->getData();
        $format = $statsTable->getDataFormats();

        $data = $this->formatData($data, $format);

        $aggregations = $statsTable->getAggregations();
        $aggregationsFormats = $statsTable->getAggregationsFormats();

        $aggregations = $this->formatLine($aggregations, $aggregationsFormats);

        $headers = $statsTable->getHeaders();

        if (!empty($headers)) {
            foreach ($data as &$line) {
                $line = array_combine($headers, $line);
            }
        }

        if (!empty($aggregations)) {
            $aggregations = array_combine($headers, $aggregations);
            $data[] = $aggregations;
        }

        return $data;
    }

    public function getMimeType() : string
    {
        return '';
    }

    protected function formatData(array $data, array $format) : array
    {
        foreach ($data as &$line) {
            $line = $this->formatLine($line, $format);
        }

        return $data;
    }

    protected function formatLine(array $line, array $format) : array
    {
        foreach ($line as $id => &$val) {
            if (array_key_exists($id, $format)) {
                $val = $this->formatValue($format[$id], $val);
            }
        }

        return $line;
    }

    /**
     * Format values
     *
     * @param Format $format
     * @param mixed  $value
     *
     * @return string
     */
    protected function formatValue(Format $format, mixed $value) : string
    {
        $decimals = $this->options->get('decimals_count');
        $decimalSep = $this->options->get('decimals_separator');
        $thousandsSep = $this->options->get('thousands_separator');

        switch ($format) {
            case Format::Date:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y');
                }
                break;

            case Format::DateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y H:i:s');
                }
                break;

            case Format::Float:
                return str_replace($decimalSep . '00', '', number_format((float) $value, $decimals, $decimalSep, $thousandsSep));

            case Format::Integer:
                return number_format((int) $value, 0, $decimalSep, $thousandsSep);

            case Format::Percent:
                return $this->formatValue(Format::Integer, $value * 100) . '%';

            case Format::Percent2:
                return $this->formatValue(Format::Float, $value * 100) . '%';

            case Format::Money:
                return $this->formatValue(Format::Integer, $value) . '€';

            case Format::Money2:
                return $this->formatValue(Format::Float, $value) . '€';
        }

        return $value;
    }
}
