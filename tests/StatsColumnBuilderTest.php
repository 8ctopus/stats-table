<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\StatsTable\Aggregation\CountAggregation;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsColumnBuilder;
use PHPUnit\Framework\TestCase;

class StatsColumnBuilderTest extends TestCase
{
    public function testCreation() : void
    {
        $aggregation = new CountAggregation('Hits');

        $values = [3, 5];
        $column = new StatsColumnBuilder($values, 'Hits', Format::INTEGER, $aggregation);

        self::assertSame($values, $column->getValues());
        self::assertSame('Hits', $column->getHeaderName());
        self::assertSame(Format::INTEGER, $column->getFormat());
        self::assertSame($aggregation, $column->getAggregation());
    }

    public function testEnsureIndexExists() : void
    {
        $values = ['2014-01-01' => 3, '2014-01-03' => 5];
        $column = new StatsColumnBuilder($values);

        $column->insureIsFilled(['2014-01-01', '2014-01-02', '2014-01-03'], 0);

        $values['2014-01-02'] = 0;

        self::assertEquals($values, $column->getValues());
    }

    public function testSetters() : void
    {
        $values = [3, 5];
        $column = new StatsColumnBuilder($values, 'Hits');

        $column->setHeaderName('Hits2');
        self::assertSame('Hits2', $column->getHeaderName());

        self::assertNull($column->getAggregation());

        $aggregation = new CountAggregation('Hits');
        $column->setAggregation($aggregation);
        self::assertSame($aggregation, $column->getAggregation());
    }
}
