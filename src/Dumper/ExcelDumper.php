<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\Tools\ParameterBag;
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
    private const FIRST_COLUMN = 1;
    private readonly ParameterBag $options;

    public function __construct(array $options = [])
    {
        $this->options = new ParameterBag(array_merge([
            'money_format' => '# ##0.00 €',
            'datetime_format' => 'dd/mm/yy hh:mm',
        ], $options));
    }

    /**
     * Dump table
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

        if ($this->options->get('zebra')) {
            if (($row % 2) === 0) {
                $bgColor = $this->options->get('zebra_color_even');
            } else {
                $bgColor = $this->options->get('zebra_color_odd');
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
            $this->applyValue($sheet, $col, $row, $value, array_key_exists($index, $formats) ? $formats[$index] : Format::String, $styleArray);
            ++$col;
        }
    }

    /**
     * Set value in specific cell
     *
     * @param Worksheet $sheet      The worksheet
     * @param int       $col        The selected column
     * @param int       $row        The selected row
     * @param mixed     $value      The values to insert
     * @param Format    $format
     * @param array     $styleArray An array representing the style
     *
     * @throws Exception
     */
    protected function applyValue(Worksheet $sheet, int $col, int $row, mixed $value, Format $format, array $styleArray = []) : void
    {
        if (0 === count($styleArray)) {
            $styleArray = $this->getDefaultStyleArrayForRow($row);
        }

        $style = new Style();
        $style->applyFromArray($styleArray);

        switch ($format) {
            case Format::Date:
                if (!$value instanceof DateTimeInterface) {
                    $date = new DateTimeImmutable($value);
                } else {
                    $date = $value;
                }

                $value = Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
                break;

            case Format::DateTime:
                if (!$value instanceof DateTimeInterface) {
                    $date = new DateTimeImmutable($value);
                } else {
                    $date = $value;
                }

                $value = Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode($this->options->get('datetime_format'));
                break;

            case Format::Float:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                break;

            case Format::Integer:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                break;

            case Format::Money:
            case Format::Money2:
                $style->getNumberFormat()->setFormatCode($this->options->get('money_format'));
                break;

            case Format::Percent:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
                break;

            case Format::Percent2:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                break;

            case Format::String:
            case Format::Link:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                break;
        }

        $sheet->duplicateStyle($style, Coordinate::stringFromColumnIndex($col) . $row);

        switch ($format) {
            case Format::String:
                $sheet->setCellValueExplicitByColumnAndRow($col, $row, $value, DataType::TYPE_STRING);

                // no break
            case Format::Link:
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
