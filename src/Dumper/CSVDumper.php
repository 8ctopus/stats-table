<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\Tools\ParameterBag;

class CSVDumper extends Dumper
{
    private readonly string $delimiter;
    private readonly string $enclosure;
    private readonly string $charset;

    public function __construct(ParameterBag|array $options = null)
    {
        $bag = new ParameterBag($options);

        $this->delimiter = $bag->get('delimiter', ',');
        $this->enclosure = $bag->get('enclosure', '"');
        $this->charset = $bag->get('charset', 'utf-8');
    }

    /**
     * Dump table
     *
     * @param  StatsTable $statsTable
     *
     * @return string
     */
    public function dump(StatsTable $statsTable) : string
    {
        $fileHandler = fopen('php://temp', 'w');

        if ($this->enableHeaders) {
            $this->writeLine($fileHandler, $statsTable->getHeaders());
        }

        foreach ($statsTable->getData() as $line) {
            $this->writeLine($fileHandler, $line, $statsTable->getDataFormats());
        }

        if ($this->enableAggregation) {
            $this->writeLine($fileHandler, $statsTable->getAggregations(), $statsTable->getAggregationsFormats());
        }

        $len = ftell($fileHandler);
        fseek($fileHandler, 0, SEEK_SET);

        return fread($fileHandler, $len);
    }

    public function getMimeType() : string
    {
        return sprintf('text/csv; charset=%s', $this->charset);
    }

    private function writeLine($fileHandler, array $line, array $formats = []) : void
    {
        foreach ($formats as $index => $format) {
            if (array_key_exists($index, $line)) {
                $line[$index] = $this->formatValue($format, $line[$index]);
            }
        }

        fputcsv($fileHandler, $line, $this->delimiter, $this->enclosure, '\\');
    }
}
