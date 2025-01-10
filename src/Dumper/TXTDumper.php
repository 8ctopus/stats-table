<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Dumper\Dumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTable;

class TXTDumper extends Dumper
{
    public function dump(StatsTable $statsTable) : string
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

        $widths = $this->measure($data);

        $output = '';

        foreach ($data as $rowIndex => $row) {
            $index = 0;

            foreach ($row as $cell) {
                $output .= str_pad((string) $cell, $widths[$index] + 1, ' ', $rowIndex ? STR_PAD_LEFT : STR_PAD_RIGHT);
                ++$index;
            }

            $output .= "\n";
        }

        return $output;
    }

    public function getMimeType() : string
    {
        return 'text/plain; charset=utf-8';
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
     * @param mixed $value
     *
     * @return string
     */
    protected function formatValue(Format $format, mixed $value) : string
    {
        $decimals = 2;
        $dec_point = ',';
        $thousands_sep = ' ';

        switch ($format) {
            case Format::fDate:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y');
                }
                break;

            case Format::fDateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y H:i:s');
                }
                break;

            case Format::fFloat:
                return str_replace($dec_point . '00', '', number_format((float) $value, $decimals, $dec_point, $thousands_sep));

            case Format::fInteger:
                return number_format((int) $value, 0, $dec_point, $thousands_sep);

            case Format::fPercent:
                return $this->formatValue(Format::fInteger, $value * 100) . '%';

            case Format::fPercent2:
                return $this->formatValue(Format::fFloat, $value * 100) . '%';

            case Format::fMoney:
                return $this->formatValue(Format::fInteger, $value) . '€';

            case Format::fMoney2:
                return $this->formatValue(Format::fFloat, $value) . '€';
        }

        return $value;
    }

    protected function measure(array $data) : array
    {
        $widths = [];

        foreach ($data as $row) {
            if (!isset($columns)) {
                $columns = count($row);
            }

            $index = 0;

            foreach ($row as $cell) {
                $widths[$index] = max($widths[$index] ?? 0, strlen((string) $cell));
                ++$index;
            }
        }

        return $widths;
    }
}
