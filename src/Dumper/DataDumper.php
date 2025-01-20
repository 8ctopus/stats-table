<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

class DataDumper extends AbstractDumper
{
    /**
     * {@inheritdoc}
     */
    public function dump(StatsTable $statsTable) : array
    {
        $data = $statsTable->getData();
        $data = $this->formatData($data, $statsTable->getDataFormats());
        $count = count($data);

        $aggregations = $statsTable->getAggregations();
        $aggregations = $this->formatLine($aggregations, $statsTable->getAggregationsFormats());

        $headers = $statsTable->getHeaders();

        if ($this->enableHeaders && !empty($headers) && $count) {
            foreach ($data as &$line) {
                $line = array_combine($headers, $line);
            }
        }

        if ($this->enableAggregation && !empty($aggregations) && $count) {
            $aggregations = array_combine(array_intersect_key($headers, $aggregations), $aggregations);
            $data[] = $aggregations;
        }

        return $data;
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
