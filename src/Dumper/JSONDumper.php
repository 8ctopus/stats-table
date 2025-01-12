<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;

class JSONDumper extends Dumper
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
        $result = [
            'data' => $statsTable->getData(),
        ];

        if ($this->enableHeaders) {
            $result['headers'] = $statsTable->getHeaders();
        }

        if ($this->enableAggregation) {
            $result['aggregations'] = $statsTable->getAggregations();
            $result['aggregationsFormats'] = $statsTable->getAggregationsFormats();
        }

        $result['formats'] = $statsTable->getDataFormats();

        // format dataset
        foreach ($result['data'] as &$line) {
            foreach ($line as $id => &$val) {
                if (array_key_exists($id, $result['formats'])) {
                    $val = $this->formatValue($result['formats'][$id], $val);
                }
            }
        }

        // format aggregations
        foreach ($result['aggregations'] as $id => &$val) {
            if (array_key_exists($id, $result['aggregationsFormats'])) {
                $val = $this->formatValue($result['aggregationsFormats'][$id], $val);
            }
        }

        return json_encode($result);
    }

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType() : string
    {
        return 'application/json';
    }

    /**
     * Format values for JSON
     *
     * @param Format $format
     * @param mixed  $value
     *
     * @return float|int|string
     */
    protected function formatValue(Format $format, mixed $value) : float|int|string
    {
        switch ($format) {
            case Format::Date:
            case Format::DateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('c');
                }
                break;

            case Format::Float:
            case Format::Money2:
                return (float) sprintf('%.2f', $value);

            case Format::Percent2:
                return (float) sprintf('%.2f', $value * 100);

            case Format::Percent:
                return (int) sprintf('%d', $value * 100);

            case Format::Integer:
            case Format::Money:
                return (int) sprintf('%d', $value);
        }

        return $value;
    }
}
