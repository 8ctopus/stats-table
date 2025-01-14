<?php

declare(strict_types=1);

namespace Tests\Dumper;

use Oct8pus\StatsTable\Aggregation\StaticAggregation;
use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

abstract class DumperTestAbstract extends TestCase
{
    protected function getData() : array
    {
        return [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];
    }

    protected function getHeaders() : array
    {
        return ['hits' => 'Hits'];
    }

    protected function getFormats() : array
    {
        return ['hits' => Format::Integer];
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
}
