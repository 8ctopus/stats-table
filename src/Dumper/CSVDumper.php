<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

class CSVDumper extends Dumper
{
    public function __construct(array $options = [])
    {
        parent::__construct(array_merge([
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
            'charset' => 'utf-8',
        ], $options));
    }

    /**
     * Dump table
     *
     * @param StatsTable $statsTable
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
        return sprintf('text/csv; charset=%s', $this->options->get('charset'));
    }

    private function writeLine($fileHandler, array $line, array $formats = []) : void
    {
        foreach ($formats as $index => $format) {
            if (array_key_exists($index, $line)) {
                $line[$index] = $this->formatValue($format, $line[$index]);
            }
        }

        fputcsv($fileHandler, $line, $this->options->get('delimiter'), $this->options->get('enclosure'), $this->options->get('escape'));
    }
}
