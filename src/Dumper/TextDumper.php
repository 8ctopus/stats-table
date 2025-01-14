<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

class TextDumper extends Dumper
{
    /**
     * Dump table
     *
     * @param StatsTable $statsTable
     *
     * @return string
     */
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
