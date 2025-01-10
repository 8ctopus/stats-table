<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\Dumper\TXTDumper;
use Oct8pus\StatsTable\StatsTable;

class TXTTest extends DumperTestAbstract
{
    public function testDump() : void
    {
        $headers = ['date' => 'Date', 'hits' => 'Nb de visites', 'subscribers' => 'Nb inscrits', 'ratio' => 'Taux de transfo', 'revenues' => 'Revenus générés'];

        $data = [
            ['date' => '2014-01-01', 'hits' => '10', 'subscribers' => 2, 'ratio' => .2, 'revenues' => 45.321],
            ['date' => '2014-01-01', 'hits' => '20', 'subscribers' => 7, 'ratio' => .35, 'revenues' => 80.754],
        ];

        $dataTypes = [
            'date' => Format::DATE,
            'hits' => Format::INTEGER,
            'subscribers' => Format::INTEGER,
            'ratio' => Format::PCT2,
            'revenues' => Format::MONEY2,
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
        $aggregationsTypes['date'] = Format::STRING;
        $aggregationsTypes['ratio'] = Format::PCT;

        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes, $metaData);

        $dumper = new TXTDumper();
        $text = $dumper->dump($statsTable);

        $excepted = <<<'TXT'
        Date       Nb de visites Nb inscrits Taux de transfo Revenus générés 
         2014-01-01            10           2             20%           45,32€
         2014-01-01            20           7             35%           80,75€
              Total            30           9              .3            126.075

        TXT;

        self::assertEquals($excepted, $text);
    }

    protected function getDumper() : DumperInterface
    {
        return new TXTDumper();
    }
}
