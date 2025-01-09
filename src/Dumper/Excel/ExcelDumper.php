<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper\Excel;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Oct8pus\StatsTable\Dumper\Dumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTable;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelDumper extends Dumper
{
    public const OPTION_ZEBRA = 'zebra';
    public const OPTION_ZEBRA_COLOR_ODD = 'zebra_color_odd';
    public const OPTION_ZEBRA_COLOR_EVEN = 'zebra_color_even';
    public const OPTION_HEADER_FORMAT = 'header_format';

    public const FORMAT_EUR = '# ##0.00 €';
    public const FORMAT_DATETIME = 'dd/mm/yy hh:mm';
    private const FIRST_COLUMN = 1;

    protected $options = [];

    /**
     * Constructor
     *
     * @param array $options An array with options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Set options defined in array. Does not replace existing ones
     *
     * @param array $options
     */
    public function setOptions(array $options) : void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Set specific option
     *
     * @param string $optionName
     * @param mixed  $optionValue
     */
    public function setOption(string $optionName, mixed $optionValue) : void
    {
        $this->options[$optionName] = $optionValue;
    }

    /**
     * Dumps the stats table
     *
     * @param StatsTable $statsTable
     *
     * @return string
     *
     * @throws Exception
     */
    public function dump(StatsTable $statsTable) : string
    {
        $excel = new Spreadsheet();

        $excel->getDefaultStyle()->applyFromArray($this->getDefaultStyleArray());

        $sheet = $excel->getActiveSheet();

        $row = 1;
        $data = $statsTable->getData();
        $width = max(count(reset($data) ?: []), count($statsTable->getHeaders() ?: []));

        // HEADERS //
        if ($this->enableHeaders) {
            $headerStyle = new Style();
            $headerStyle->applyFromArray($this->getHeadersStyleArray());

            $col = self::FIRST_COLUMN;
            foreach ($statsTable->getHeaders() as $header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $header);
                ++$col;
            }
            $sheet->duplicateStyle($headerStyle, 'A1:' . Coordinate::stringFromColumnIndex($width) . '1');
            ++$row;
        }

        // DATA //
        foreach ($statsTable->getData() as $data) {
            $this->applyValues($sheet, $row, $data, $statsTable->getDataFormats());
            ++$row;
        }

        // AGGREGATIONS //
        if ($this->enableAggregation) {
            $this->applyValues($sheet, $row, $statsTable->getAggregations(), $statsTable->getAggregationsFormats(), $this->getAggregationsStyleArray());
        }

        // FINAL FORMATTING //
        for ($col = self::FIRST_COLUMN; $col < self::FIRST_COLUMN + $width; ++$col) {
            $sheet
                ->getColumnDimension(Coordinate::stringFromColumnIndex($col))
                ->setAutoSize(true);
        }

        $xlsDumper = new Xlsx($excel);
        $pFilename = @tempnam(sys_get_temp_dir(), 'phpxltmp');
        $xlsDumper->save($pFilename);
        $contents = file_get_contents($pFilename);
        @unlink($pFilename);

        unset($excel, $xlsDumper);

        return $contents;
    }

    /**
     * Gets an option
     *
     * @param $optionName
     *
     * @return null
     */
    public function getOption($optionName)
    {
        if (array_key_exists($optionName, $this->options)) {
            return $this->options[$optionName];
        } else {
            return null;
        }
    }

    public function getMimeType() : string
    {
        return 'application/vnd.ms-office; charset=binary';
    }

    /**
     * Get default style
     *
     * @return array
     */
    protected function getDefaultStyleArray() : array
    {
        return [
            'font' => ['name' => 'Arial', 'size' => 9],
        ];
    }

    /**
     * Get default style for a filled cell
     *
     * @return array
     */
    protected function getDefaultStyleForFilledCells() : array
    {
        return array_merge_recursive(
            $this->getDefaultStyleArray(),
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]
        );
    }

    /**
     * Get default style for a given row
     *
     * @param int $row
     *
     * @return array
     */
    protected function getDefaultStyleArrayForRow(int $row) : array
    {
        $style = $this->getDefaultStyleForFilledCells();

        if ($this->getOption(self::OPTION_ZEBRA)) {
            if (($row % 2) === 0) {
                $bgColor = $this->getOption(self::OPTION_ZEBRA_COLOR_EVEN);
            } else {
                $bgColor = $this->getOption(self::OPTION_ZEBRA_COLOR_ODD);
            }

            if ($bgColor) {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => $bgColor],
                ];
            }
        }

        return $style;
    }

    /**
     * Get style for headers
     *
     * @return array
     */
    protected function getHeadersStyleArray() : array
    {
        return array_merge_recursive(
            $this->getDefaultStyleForFilledCells(),
            [
                'borders' => [
                    'bottom' => [
                        'style' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD0D0D0'],
                ],
                'font' => ['bold' => true],
            ]
        );
    }

    /**
     * Get style for aggregations
     *
     * @return array
     */
    protected function getAggregationsStyleArray() : array
    {
        return array_merge_recursive(
            $this->getDefaultStyleForFilledCells(),
            [
                'borders' => [
                    'top' => [
                        'style' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD0D0D0'],
                ],
                'font' => ['bold' => true],
            ]
        );
    }

    /**
     * Set values in specific row
     *
     * @param Worksheet $sheet      The worksheet
     * @param int       $row        The selected row
     * @param array     $values     The values to insert
     * @param array     $formats    Associative arrays with formats
     * @param array     $styleArray An array representing the style
     *
     * @throws Exception
     */
    protected function applyValues(Worksheet $sheet, int $row, array $values, array $formats, array $styleArray = []) : void
    {
        $col = self::FIRST_COLUMN;
        foreach ($values as $index => $value) {
            $this->applyValue($sheet, $col, $row, $value, array_key_exists($index, $formats) ? $formats[$index] : Format::STRING, $styleArray);
            ++$col;
        }
    }

    /**
     * Set value in specific cell
     *
     * @param Worksheet $sheet      The worksheet
     * @param int       $col        The selected column
     * @param int       $row        The selected row
     * @param array     $value      The values to insert
     * @param array     $format     Associative arrays with formats
     * @param array     $styleArray An array representing the style
     *
     * @throws Exception
     */
    protected function applyValue(Worksheet $sheet, int $col, int $row, $value, $format, array $styleArray = []) : void
    {
        if (0 === count($styleArray)) {
            $styleArray = $this->getDefaultStyleArrayForRow($row);
        }

        $style = new Style();
        $style->applyFromArray($styleArray);

        switch ($format) {
            case Format::DATE:
                if (!$value instanceof DateTimeInterface) {
                    $date = new DateTimeImmutable($value);
                } else {
                    $date = $value;
                }
                $value = Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
                break;

            case Format::DATETIME:
                if (!$value instanceof DateTimeInterface) {
                    $date = new DateTimeImmutable($value);
                } else {
                    $date = $value;
                }
                $value = Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(self::FORMAT_DATETIME);
                break;

            case Format::FLOAT2:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                break;

            case Format::INTEGER:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                break;

            case Format::MONEY:
            case Format::MONEY2:
                $style->getNumberFormat()->setFormatCode(self::FORMAT_EUR);
                break;

            case Format::PCT:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
                break;

            case Format::PCT2:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                break;

            case Format::STRING:
            case Format::LINK:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                break;
        }

        $sheet->duplicateStyle($style, Coordinate::stringFromColumnIndex($col) . $row);

        switch ($format) {
            case Format::STRING:
                $sheet->setCellValueExplicitByColumnAndRow($col, $row, $value, DataType::TYPE_STRING);

                // no break
            case Format::LINK:
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $sheet->getCellByColumnAndRow($col, $row)->getHyperlink()->setUrl($value);
                }
                break;

            default:
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                break;
        }
    }
}
