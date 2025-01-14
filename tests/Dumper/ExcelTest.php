<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Dumper\Dumper;
use Oct8pus\StatsTable\Dumper\ExcelDumper;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;

class ExcelTest extends DumperTestAbstract
{
    public function test() : void
    {
        self::expectNotToPerformAssertions();

        $headers = [
            'date' => 'Date',
            'hits' => 'Nb de visites',
            'subscribers' => 'Nb inscrits',
            'ratio' => 'Taux de transfo',
            'revenues' => 'Revenus générés'
        ];

        $data = [
            ['date' => '2014-01-01', 'hits' => '10', 'subscribers' => 2, 'ratio' => .2, 'revenues' => 45.321],
            ['date' => '2014-02-01', 'hits' => '20', 'subscribers' => 7, 'ratio' => .35, 'revenues' => 80.754],
        ];

        $dataTypes = [
            'date' => Format::Date,
            'hits' => Format::Integer,
            'subscribers' => Format::Integer,
            'ratio' => Format::Percent2,
            'revenues' => Format::Money2,
        ];

        $aggregations = [
            'date' => 'Total',
            'hits' => '30',
            'subscribers' => '9',
            'ratio' => '.3',
            'revenues' => 126.075,
        ];

        $aggregationsTypes = $dataTypes;
        $aggregationsTypes['date'] = Format::String;
        $aggregationsTypes['ratio'] = Format::Percent;

        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes);
        $excelDumper = new ExcelDumper([
            'zebra' => true,
            'zebra_color_odd' => 'eeeeee',
        ]);

        $excelContents = $excelDumper->dump($statsTable);

        file_put_contents(sys_get_temp_dir() . '/test.xls', $excelContents);

        $dataTypes['date'] = Format::DateTime;
        $dataTypes['revenues'] = Format::Float;
        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes);

        $excelDumper = new ExcelDumper([
            'zebra' => false,
            'zebra_color_odd' => 'eeeeee',
        ]);

        $excelContents = $excelDumper->dump($statsTable);

        file_put_contents(sys_get_temp_dir() . '/test2.xls', $excelContents);
    }

    public function testEmpty() : void
    {
        self::expectNotToPerformAssertions();

        $statsTable = new StatsTable([], []);
        $excelDumper = new ExcelDumper();
        $excelDumper->dump($statsTable);
    }

    protected function getDumper() : Dumper
    {
        return new ExcelDumper();
    }
}
