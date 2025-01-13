<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;

class DataDumper extends Dumper
{
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
            array_unshift($data, $headers);
        }

        $aggregations = $statsTable->getAggregations();

        if (!empty($aggregations)) {
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
        $decimals = 2;
        $dec_point = ',';
        $thousands_sep = ' ';

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
                return str_replace($dec_point . '00', '', number_format((float) $value, $decimals, $dec_point, $thousands_sep));

            case Format::Integer:
                return number_format((int) $value, 0, $dec_point, $thousands_sep);

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
