<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

class DataDumper extends AbstractDumper
{
    /**
     * Dump table
     *
     * @param StatsTable $statsTable
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

        if ($this->enableHeaders && !empty($headers)) {
            foreach ($data as &$line) {
                $line = array_combine($headers, $line);
            }
        }

        if ($this->enableAggregation && !empty($aggregations)) {
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
}
