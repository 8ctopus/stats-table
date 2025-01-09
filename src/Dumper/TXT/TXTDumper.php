<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper\TXT;

use Oct8pus\StatsTable\Dumper\Dumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTable;

class TXTDumper extends Dumper
{
    public function dump(StatsTable $statsTable)
    {
        $data = $statsTable->getData();
        $format = $statsTable->getDataFormats();
        $aggregations = $statsTable->getAggregations();
        $aggregationsFormats = $statsTable->getAggregationsFormats();

        $data = $this->formatData($data, $format);
        $aggregations = $this->formatLine($aggregations, $aggregationsFormats);

        $headers = $statsTable->getHeaders();

        if (!empty($headers)) {
            array_unshift($data, $headers);
        }

        $aggregations = $statsTable->getAggregations();

        if (!empty($aggregations)) {
            array_push($data, $aggregations);
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

    public function getMimeType()
    {
        return 'text/plain; charset=utf-8';
    }

    protected function formatData($data, $format)
    {
        foreach ($data as &$line) {
            $line = $this->formatLine($line, $format);
        }

        return $data;
    }

    protected function formatLine($line, $format)
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
     * @param $format
     * @param $value
     * @return float|int|string
     */
    protected function formatValue($format, $value)
    {
        $decimals = 2;
        $dec_point = ',';
        $thousands_sep = ' ';

        switch ($format) {
            case Format::DATE:
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('d/m/Y');
                }
                break;

            case Format::DATETIME:
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('d/m/Y H:i:s');
                }
                break;

            case Format::FLOAT2:
                return str_replace($dec_point."00", "",number_format(floatval($value), $decimals, $dec_point, $thousands_sep));

            case Format::INTEGER:
                return number_format(intval($value), 0, $dec_point, $thousands_sep);

            case Format::PCT:
                return $this->formatValue(Format::INTEGER, $value*100)."%";

            case Format::PCT2:
                return $this->formatValue(Format::FLOAT2, $value*100)."%";

            case Format::MONEY:
                return $this->formatValue(Format::INTEGER, $value)."€";

            case Format::MONEY2:
                return $this->formatValue(Format::FLOAT2, $value)."€";
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
