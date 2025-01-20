<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

class TextDumper extends AbstractDumper
{
    /**
     * {@inheritdoc}
     */
    public function dump(StatsTable $statsTable) : string
    {
        $data = $statsTable->getData();
        $data = $this->formatData($data, $statsTable->getDataFormats());
        $count = count($data);

        $aggregations = $statsTable->getAggregations();
        $aggregations = $this->formatLine($aggregations, $statsTable->getAggregationsFormats());

        $headers = $statsTable->getHeaders();

        if ($this->enableHeaders && !empty($headers) && $count) {
            array_unshift($data, $headers);
        }

        if ($this->enableAggregation && !empty($aggregations) && $count) {
            $data[] = $aggregations;
        }

        $widths = $this->measure($data);

        $output = '';

        foreach ($data as $row) {
            $index = 0;

            foreach ($row as $cell) {
                $output .= str_pad((string) $cell, $widths[$index] + 2, ' ', STR_PAD_LEFT);
                ++$index;
            }

            $output .= "\n";
        }

        return $output;
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
