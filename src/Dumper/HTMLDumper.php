<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HTMLDumper extends AbstractDumper
{
    private Environment $twig;

    public function __construct(array $options = [])
    {
        parent::__construct(array_merge([
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
            'charset' => 'utf-8',
            'templateFolder' => __DIR__ . '/../Resources/views',
            'template' => 'statsTable.html.twig',
            'templateOptions' => [],
        ], $options));

        $this->twig = new Environment(new FilesystemLoader($this->options->get('templateFolder')));
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
        $data = $statsTable->getData();
        $format = $statsTable->getDataFormats();
        $aggregations = $statsTable->getAggregations();
        $aggregationsFormats = $statsTable->getAggregationsFormats();
        $metaData = $statsTable->getMetaData();

        $data = $this->formatData($data, $format);
        $aggregations = $this->formatLine($aggregations, $aggregationsFormats);

        $params = [
            'headers' => $statsTable->getHeaders(),
            'data' => $data,
            'aggregations' => $aggregations,
            'metaData' => $metaData,
        ];

        $params = array_merge($params, $this->options->get('templateOptions'));

        return $this->twig->render($this->options->get('template'), $params);
    }

    public function setTwig(Environment $twig) : void
    {
        $this->twig = $twig;
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
