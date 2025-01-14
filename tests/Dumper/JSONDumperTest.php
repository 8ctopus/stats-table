<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Dumper\JSONDumper;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;

class JSONDumperTest extends DumperTestAbstract
{
    public function testJSON() : void
    {
        $jsonDumper = new JSONDumper();

        $formats = $this->getFormats();

        foreach ($formats as &$format) {
            $format = $format->value;
        }

        // with all values
        self::assertEquals([
            'headers' => $this->getHeaders(),
            'data' => $this->getData(),
            'aggregations' => ['hits' => 'value'],
            'aggregationsFormats' => ['hits' => Format::String->value],
            'formats' => $formats,
        ], json_decode($jsonDumper->dump($this->getStatsTable()), true));
    }

    public function testJSONPct() : void
    {
        $jsonDumper = new JSONDumper();

        $data = [['pct' => .3123]];
        $statsTable = new StatsTable(
            $data,
            array_keys(current($data)),
            [],
            ['pct' => Format::Percent]
        );

        self::assertEquals([
            'headers' => ['pct'],
            'data' => [['pct' => 31]],
            'aggregations' => [],
            'aggregationsFormats' => [],
            'formats' => ['pct' => Format::Percent->value],
        ], json_decode($jsonDumper->dump($statsTable), true));
    }

    public function testJSONPct2() : void
    {
        $jsonDumper = new JSONDumper();

        $data = [
            [
                'pct' => .3123,
            ],
        ];

        $statsTable = new StatsTable(
            $data,
            array_keys(current($data)),
            [],
            ['pct' => Format::Percent2]
        );

        self::assertEquals([
            'headers' => ['pct'],
            'data' => [['pct' => 31.23]],
            'aggregations' => [],
            'aggregationsFormats' => [],
            'formats' => ['pct' => Format::Percent2->value],
        ], json_decode($jsonDumper->dump($statsTable), true));
    }

    protected function getDumper() : DumperInterface
    {
        return new JSONDumper();
    }
}
