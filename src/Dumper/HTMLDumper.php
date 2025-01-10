<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeInterface;
use Oct8pus\StatsTable\Dumper\Dumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\Tools\ParameterBag;
use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;

class HTMLDumper extends Dumper
{
    /**
     * @var string
     */
    protected string $template;

    /**
     * @var string
     */
    protected string $templateFolder;

    /**
     * @var array
     */
    protected array $templateOptions;

    /**
     * @var Twig
     */
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
     * @param mixed $value
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
            case Format::fDate:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y');
                }
                break;

            case Format::fDateTime:
                if ($value instanceof DateTimeInterface) {
                    return $value->format('d/m/Y H:i:s');
                }
                break;

            case Format::fFloat:
                return str_replace($dec_point . '00', '', number_format((float) $value, $decimals, $dec_point, $thousands_sep));

            case Format::fInteger:
                return number_format((int) $value, 0, $dec_point, $thousands_sep);

            case Format::fPercent:
                return $this->formatValue(Format::fInteger, $value * 100) . '%';

            case Format::fPercent2:
                return $this->formatValue(Format::fFloat, $value * 100) . '%';

            case Format::fMoney:
                return $this->formatValue(Format::fInteger, $value) . '€';

            case Format::fMoney2:
                return $this->formatValue(Format::fFloat, $value) . '€';
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
