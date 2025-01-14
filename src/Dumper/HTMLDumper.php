<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\Tools\ParameterBag;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HTMLDumper extends Dumper
{
    private Environment $twig;

    public function __construct(array $options = [])
    {
        $this->options = new ParameterBag(array_merge([
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
     * @param  StatsTable $statsTable
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

    public function getMimeType() : string
    {
        return 'text/html; charset=utf-8';
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

    /**
     * Format value
     *
     * @param Format $format
     * @param mixed  $value
     *
     * @return string
     */
    protected function formatValue(Format $format, mixed $value) : string
    {
        $decimals = $this->options->get('decimals_count');
        $decimalSep = $this->options->get('decimals_separator');
        $thousandsSep = $this->options->get('thousands_separator');

        switch ($format) {
            case Format::Date:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y');
                }
                break;

            case Format::DateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y H:i:s');
                }
                break;

            case Format::Float:
                return str_replace($decimalSep . '00', '', number_format((float) $value, $decimals, $decimalSep, $thousandsSep));

            case Format::Integer:
                return number_format((int) $value, 0, $decimalSep, $thousandsSep);

            case Format::Percent:
                return $this->formatValue(Format::Integer, $value * 100) . '%';

            case Format::Percent2:
                return $this->formatValue(Format::Float, $value * 100) . '%';

            case Format::Money:
                return $this->formatValue(Format::Integer, $value) . '€';

            case Format::Money2:
                return $this->formatValue(Format::Float, $value) . '€';
        }

        return $value;
    }

    public function setTwig(Environment $twig) : void
    {
        $this->twig = $twig;
    }
}
