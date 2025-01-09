<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Aggregation\StaticAggregation;
use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Dumper\Excel\ExcelDumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

abstract class DumperTestAbstract extends TestCase
{
    protected function getData() : array
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];

        return $table;
    }

    protected function getHeaders() : array
    {
        return ['hits' => 'Hits'];
    }

    protected function getFormats() : array
    {
        return ['hits' => Format::INTEGER];
    }

    protected function getAggregations() : array
    {
        return ['hits' => new StaticAggregation('value')];
    }

    protected function getStatsTableBuilder() : StatsTableBuilder
    {
        return new StatsTableBuilder($this->getData(), $this->getHeaders(), $this->getFormats(), $this->getAggregations());
    }

    protected function getStatsTable() : StatsTable
    {
        return $this->getStatsTableBuilder()->build();
    }

    abstract protected function getDumper() : DumperInterface;

    public function testLink() : void
    {
        self::expectNotToPerformAssertions();
        $dumper = $this->getDumper();

        $statsTable = new StatsTable([['http://example.org']], ['link'], [''], [Format::LINK], [Format::STRING]);
        $dumper->dump($statsTable);

        // Should not fail if link is not valid
        $statsTable = new StatsTable([['']], ['link'], [''], [Format::LINK], [Format::STRING]);
        $dumper->dump($statsTable);
    }
}
