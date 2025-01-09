<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\Dumper\JSON\JSONDumper;
use Oct8pus\StatsTable\StatsTable;

class JSONTest extends DumperTestAbstract
{
    public function testJSON() : void
    {
        $jsonDumper = new JSONDumper();

        // With all values
        self::assertEquals(array(
            'headers' => $this->getHeaders(),
            'data' => $this->getData(),
            'aggregations' => ['hits' => 'value'],
            'aggregationsFormats' => ['hits'=>Format::STRING],
            'formats'=> $this->getFormats(),

        ), json_decode($jsonDumper->dump($this->getStatsTable()), true));
    }

    public function testJSONPct() : void
    {
        $jsonDumper = new JSONDumper();

        $data = [['pct' => .3123]];
        $statsTable = new StatsTable(
            $data,
            array_keys(current($data)),
            [],
            ['pct' => Format::PCT]
        );

        self::assertEquals(array(
            'headers' => ['pct'],
            'data' => [['pct' => 31]],
            'aggregations' => [],
            'aggregationsFormats' => [],
            'formats' => ['pct' => Format::PCT]
        ), json_decode($jsonDumper->dump($statsTable), true));
    }

    public function testJSONPct2() : void
    {
        $jsonDumper = new JSONDumper();

        $data = [['pct' => .3123]];
        $statsTable = new StatsTable(
            $data,
            array_keys(current($data)),
            [],
            ['pct' => Format::PCT2]
        );

        self::assertEquals(array(
            'headers' => ['pct'],
            'data' => [['pct' => 31.23]],
            'aggregations' => [],
            'aggregationsFormats' => [],
            'formats' => ['pct' => Format::PCT2]
        ), json_decode($jsonDumper->dump($statsTable), true));
    }

    protected function getDumper() : DumperInterface
    {
        return new JSONDumper();
    }
}
