<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Dumper\DataDumper;
use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;

class DataTest extends DumperTestAbstract
{
    public function testDump() : void
    {
        $headers = [
            'date' => 'Date',
            'hits' => 'Nb de visites',
            'subscribers' => 'Nb inscrits',
            'ratio' => 'Taux de transfo',
            'revenues' => 'Revenus générés',
        ];

        $data = [
            ['date' => '2014-01-01', 'hits' => '10', 'subscribers' => 2, 'ratio' => .2, 'revenues' => 45.321],
            ['date' => '2014-01-01', 'hits' => '20', 'subscribers' => 7, 'ratio' => .35, 'revenues' => 80.754],
        ];

        $dataTypes = [
            'date' => Format::Date,
            'hits' => Format::Integer,
            'subscribers' => Format::Integer,
            'ratio' => Format::Percent,
            'revenues' => Format::Money2,
        ];

        $aggregations = [
            'date' => 'Total',
            'hits' => '30',
            'subscribers' => '9',
            'ratio' => '.3',
            'revenues' => 126.075,
        ];

        $metaData = [
            'date' => ['description' => 'Date of the stats'],
            'hits' => ['description' => 'Number of hits'],
        ];

        $aggregationsTypes = $dataTypes;
        $aggregationsTypes['date'] = Format::String;
        $aggregationsTypes['ratio'] = Format::Percent;

        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes, $metaData);

        $dumper = new DataDumper();
        $data = $dumper->dump($statsTable);

        $excepted = [
            [
                'Date' => '2014-01-01',
                'Nb de visites' => '10',
                'Nb inscrits' => '2',
                'Taux de transfo' => '20%',
                'Revenus générés' => '45.32 €',
            ], [
                'Date' => '2014-01-01',
                'Nb de visites' => '20',
                'Nb inscrits' => '7',
                'Taux de transfo' => '35%',
                'Revenus générés' => '80.75 €',
            ], [
                'Date' => 'Total',
                'Nb de visites' => '30',
                'Nb inscrits' => '9',
                'Taux de transfo' => '30%',
                'Revenus générés' => '126.08 €',
            ],
        ];

        self::assertEquals($excepted, $data);
    }

    protected function getDumper() : DumperInterface
    {
        return new DataDumper();
    }
}
