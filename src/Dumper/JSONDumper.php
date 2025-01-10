<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Dumper\Dumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTable;

class JSONDumper extends Dumper
{
    /**
     * Dump Data
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

        // Format value for each line of dataset
        foreach ($result['data'] as &$line) {
            foreach ($line as $id => &$val) {
                if (array_key_exists($id, $result['formats'])) {
                    $val = $this->formatValue($result['formats'][$id], $val);
                }
            }
        }

        // Format value for each value of aggregations
        foreach ($result['aggregations'] as $id => &$val) {
            if (array_key_exists($id, $result['aggregationsFormats'])) {
                $val = $this->formatValue($result['aggregationsFormats'][$id], $val);
            }
        }

        return json_encode($result);
    }

    /**
     * Get mime type of dumper
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
     * @param $format
     * @param $value
     *
     * @return float|int|string
     */
    protected function formatValue($format, $value) : float|int|string
    {
        switch ($format) {
            case Format::DATE:
            case Format::DATETIME:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('c');
                }
                break;

            case Format::FLOAT2:
            case Format::MONEY2:
                return (float) sprintf('%.2f', $value);

            case Format::PCT2:
                return (float) sprintf('%.2f', $value * 100);

            case Format::PCT:
                return (int) sprintf('%d', $value * 100);

            case Format::INTEGER:
            case Format::MONEY:
                return (int) sprintf('%d', $value);
        }

        return $value;
    }
}
