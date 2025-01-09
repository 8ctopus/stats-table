<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\Dumper\HTML\HTMLDumper;
use Oct8pus\StatsTable\StatsTable;
use DOMDocument;

class HTMLTest extends DumperTestAbstract
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
            'date' => ['description'=>'Date of the stats'],
            'hits' => ['description'=>'Number of hits'],
        ];

        $aggregationsTypes = $dataTypes;
        $aggregationsTypes['date'] = Format::STRING;
        $aggregationsTypes['ratio'] = Format::PCT;

        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes, $metaData);

        $dumper = new HTMLDumper();
        $html = $dumper->dump($statsTable);
        $doc = new DOMDocument();
        $doc->loadXML($html);

        $expectedDoc = new DOMDocument();
        $expectedDoc->load(__DIR__ . '/Fixtures/test.html');

        self::assertEquals($expectedDoc, $doc);


        // Test with a custom template
        $dumper = new HTMLDumper([
            'template'        => 'custom.html.twig',
            'templateFolder'  => __DIR__ . '/Fixtures/template',
            'templateOptions' => ['title' => 'My title test']
        ]);
        $html = $dumper->dump($statsTable);

        $doc = new DOMDocument();
        $doc->loadXML($html);

        $expectedDoc = new DOMDocument();
        $expectedDoc->load(__DIR__ . '/Fixtures/test-custom.html');

        self::assertEquals($expectedDoc, $doc);
    }

    protected function getDumper() : DumperInterface
    {
        return new HTMLDumper();
    }
}
