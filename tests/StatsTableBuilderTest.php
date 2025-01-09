<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use Oct8pus\StatsTable\Aggregation\StaticAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsColumnBuilder;
use Oct8pus\StatsTable\StatsTable;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

class StatsTableBuilderTest extends TestCase
{
    public function testGetters() : void
    {
        $table = [
            ['hits' => 12, 'subscribers' => 3],
            ['hits' => 25, 'subscribers' => 4],
        ];

        $statsTable = new StatsTableBuilder(
            $table,
            ['hits' => 'Hits', 'subscribers' => 'Subscribers']
        );

        self::assertEquals(new StatsColumnBuilder([12, 25], 'Hits'), $statsTable->getColumn('hits'));
    }

    public function testAdditionalIndexes() : void
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];

        $defaultValues = ['hits' => 0];

        $wishedColumn = [
            '2014-01-01' => 12,
            '2014-01-02' => 0,
            '2014-01-03' => 14,
        ];

        $statsTable = new StatsTableBuilder(
            $table,
            [],
            [],
            [],
            [],
            $defaultValues,
            array_keys($wishedColumn)
        );

        self::assertEquals(
            new StatsColumnBuilder($wishedColumn, 'hits'),
            $statsTable->getColumn('hits')
        );

        self::assertSame(array_keys($wishedColumn), array_keys($statsTable->getColumn('hits')->getValues()));
    }

    public function testAddIndexAsColumn() : void
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];

        $statsTable = new StatsTableBuilder($table);
        $statsTable->addIndexesAsColumn('date', 'Date');

        $dateColumn = new StatsColumnBuilder(
            [
                '2014-01-01' => '2014-01-01',
                '2014-01-03' => '2014-01-03',
            ],
            'Date'
        );

        self::assertEquals($dateColumn, $statsTable->getColumn('date'));
    }

    public function testBuildWithAggregation() : void
    {
        $data = $this->_getTestData();
        $statsTable = new StatsTableBuilder(
            $data,
            ['hits' => 'Hits'],
            ['hits' => Format::INTEGER],
            ['hits' => new SumAggregation('hits')]
        );

        $stats = $statsTable->build();
        self::assertEquals(new StatsTable(
            $data,
            ['hits' => 'Hits'],
            ['hits' => 26],
            ['hits' => Format::INTEGER],
            ['hits' => Format::INTEGER],
            ['hits' => []]
        ), $stats);
    }

    public function testBuildWithoutAggregation() : void
    {
        $data = $this->_getTestData();
        $statsTable = new StatsTableBuilder(
            $data,
            ['hits' => 'Hits']
        );

        $stats = $statsTable->build();
        self::assertEquals(new StatsTable(
            $data,
            ['hits' => 'Hits'],
            ['hits' => null],
            ['hits' => null],
            [],
            ['hits' => []]
        ), $stats);
    }

    public function testBuildWithoutData() : void
    {
        $statsTable = new StatsTableBuilder(
            [],
            ['hits' => 'Hits']
        );

        $stats = $statsTable->build();
        self::assertEquals(new StatsTable(
            [],
            ['hits' => 'Hits'],
            ['hits' => null],
            ['hits' => null],
            [],
            ['hits' => []]
        ), $stats);
    }

    public function testBuildWithoutDataAndWithAggregation() : void
    {
        $statsTable = new StatsTableBuilder(
            [],
            ['hits' => 'Hits'],
            ['hits' => Format::INTEGER],
            ['hits' => new SumAggregation('hits')]
        );

        $stats = $statsTable->build();
        self::assertEquals(new StatsTable(
            [],
            ['hits' => 'Hits'],
            ['hits' => 0],
            ['hits' => Format::INTEGER],
            ['hits' => Format::INTEGER],
            ['hits' => []]
        ), $stats);
    }

    public function testMissingColumn() : void
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => [],
        ];

        $defaultValues = ['hits' => 0];

        $statsTable = new StatsTableBuilder(
            $table,
            ['hits' => 'Hits'],
            [],
            [],
            array_keys($defaultValues),
            $defaultValues
        );

        $wishedColumn = [
            '2014-01-01' => 12,
            '2014-01-03' => 0,
        ];

        self::assertEquals(new StatsColumnBuilder($wishedColumn, 'Hits'), $statsTable->getColumn('hits'));
    }

    public function testInvalidColumn() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $table = [['hits' => 0]];
        $statsTable = new StatsTableBuilder($table);

        $statsTable->getColumn('invalidColumn');
    }

    public function testOrderColumns() : void
    {
        $table = ['a' => 'value1', 'b' => 'value2', 'c' => 'value3'];
        $expectedTable = ['c' => 'value3', 'a' => 'value1'];

        self::assertEquals($expectedTable, StatsTableBuilder::orderColumns($table, ['c', 'a']));

        self::assertEquals($table, StatsTableBuilder::orderColumns($table, []));
    }

    public function testBuildWithOrder() : void
    {
        $table = [
            ['a' => 'a', 'b' => 'b', 'c' => 'c'],
            ['a' => 'A', 'b' => 'B', 'c' => 'C'],
        ];
        $headers = [
            'a' => 'Alpha',
            'b' => 'Bravo',
            'c' => 'Charly',
        ];

        $statsTableBuilder = new StatsTableBuilder(
            $table,
            $headers,
            [Format::STRING, Format::STRING]
        );

        $statsTable = $statsTableBuilder->build(['c', 'a']);
        self::assertEquals(
            ['c' => 'Charly', 'a' => 'Alpha'],
            $statsTable->getHeaders()
        );
    }

    public function testGroupBy() : void
    {
        $table = [
            ['tag' => 'one', 'subtag' => 'morning', 'hits' => 2],
            ['tag' => 'one', 'subtag' => 'afternoon', 'hits' => 3],
            ['tag' => 'two', 'subtag' => 'morning', 'hits' => 4],
        ];
        $statsTableBuilder = new StatsTableBuilder(
            $table,
            ['tag' => 'Tag', 'subtag' => 'When', 'hits' => 'Hits'],
            ['tag' => Format::STRING, 'subtag' => Format::STRING, 'hits' => Format::INTEGER],
            [
                'tag' => new StaticAggregation('Tag'),
                'subtag' => new StaticAggregation('Sub tag'),
                'hits' => new SumAggregation('hits', Format::INTEGER),
            ]
        );

        $groupedByStatsTableBuilder = $statsTableBuilder->groupBy(['tag'], ['subtag']);

        self::assertSame(2, count($groupedByStatsTableBuilder->getColumns()));

        self::assertEquals(
            ['one', 'two'],
            $groupedByStatsTableBuilder->getColumn('tag')->getValues()
        );

        self::assertEquals(
            [5, 4],
            $groupedByStatsTableBuilder->getColumn('hits')->getValues()
        );

        self::assertSame(
            'Tag',
            $groupedByStatsTableBuilder->getColumn('tag')->getAggregation()->aggregate($groupedByStatsTableBuilder)
        );
        self::assertSame(
            9,
            $groupedByStatsTableBuilder->getColumn('hits')->getAggregation()->aggregate($groupedByStatsTableBuilder)
        );
    }

    private function _getTestData()
    {
        // Data of test
        return [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];
    }
}
