<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\Tools\ParameterBag;
use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;

class HTMLDumper extends Dumper
{
    protected readonly string $template;
    protected readonly string $templateFolder;
    protected readonly array $templateOptions;
    protected Twig $twig;

    public function __construct($options = [])
    {
        $options = new ParameterBag($options);

        $this->template = $options->get('template', $this->getDefaultTemplate());
        $this->templateFolder = $options->get('templateFolder', $this->getDefaultTemplateFolder());
        $this->twig = new Twig(new TwigFilesystemLoader($this->templateFolder));
        $this->templateOptions = $options->get('templateOptions', []);
    }

    public function setTwig(Twig $twig) : void
    {
        $this->twig = $twig;
    }

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

        $params = array_merge($params, $this->templateOptions);

        return $this->twig->render($this->template, $params);
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
     * Format values for HTML View
     *
     * @param Format $format
     * @param mixed  $value
     *
     * @return string
     */
    protected function formatValue(Format $format, mixed $value) : string
    {
        // TODO : Put in parameters
        $decimals = 2;
        $dec_point = ',';
        $thousands_sep = ' ';

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
                return str_replace($dec_point . '00', '', number_format((float) $value, $decimals, $dec_point, $thousands_sep));

            case Format::Integer:
                return number_format((int) $value, 0, $dec_point, $thousands_sep);

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

    protected function getDefaultTemplateFolder() : string
    {
        return __DIR__ . '/../Resources/views';
    }

    protected function getDefaultTemplate() : string
    {
        return 'statsTable.html.twig';
    }
}
