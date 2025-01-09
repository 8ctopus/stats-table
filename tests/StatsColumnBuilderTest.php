<?php

namespace Tests;

use IgraalOSL\StatsTable\Aggregation\AggregationInterface;
use IgraalOSL\StatsTable\Aggregation\CountAggregation;
use IgraalOSL\StatsTable\StatsColumnBuilder;
use PHPUnit\Framework\TestCase;

class StatsColumnBuilderTest extends TestCase
{
    public function testCreation()
    {
        $aggregation = new CountAggregation('Hits');

        $values = [3, 5];
        $column = new StatsColumnBuilder($values, 'Hits', 'format', $aggregation);

        $this->assertEquals($values, $column->getValues());
        $this->assertEquals('Hits', $column->getHeaderName());
        $this->assertEquals('format', $column->getFormat());
        $this->assertEquals($aggregation, $column->getAggregation());
    }

    public function testEnsureIndexExists()
    {
        $values = ['2014-01-01' => 3, '2014-01-03' => 5];
        $column = new StatsColumnBuilder($values);

        $column->insureIsFilled(['2014-01-01', '2014-01-02', '2014-01-03'], 0);

        $values['2014-01-02'] = 0;

        $this->assertEquals($values, $column->getValues());
    }

    public function testSetters()
    {
        $values = [3, 5];
        $column = new StatsColumnBuilder($values, 'Hits');

        $column->setHeaderName('Hits2');
        $this->assertEquals('Hits2', $column->getHeaderName());

        $this->assertNull($column->getAggregation());

        $aggregation = new CountAggregation('Hits');
        $column->setAggregation($aggregation);
        $this->assertEquals($aggregation, $column->getAggregation());
    }
}
