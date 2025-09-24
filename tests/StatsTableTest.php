<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\StatsTable\Direction;
use Oct8pus\StatsTable\StatsTable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StatsTableTest extends TestCase
{
    public function testRemoveColumn() : void
    {
        $statsTable = new StatsTable(
            [
                ['a' => 'a', 'b' => 'b'],
                ['a' => 'A', 'b' => 'B'],
            ],
            ['a' => 'Alpha', 'b' => 'Bravo']
        );

        $statsTable->removeColumn('b');

        self::assertSame(['a' => 'Alpha'], $statsTable->getHeaders());

        self::assertSame(
            [
                ['a' => 'a'],
                ['a' => 'A'],
            ],
            $statsTable->getData()
        );
    }

    #[DataProvider('provideSortOneColumnCases')]
    public function testSortOneColumn(string $columnName, $direction, $expected) : void
    {
        $statsTable = $this->_getSimpleTestData();
        $statsTable->sortByColumn($columnName, $direction);
        self::assertSame($expected, $statsTable->getData());
    }

    public static function provideSortOneColumnCases() : iterable
    {
        return [
            [
                'age',
                Direction::Ascending,
                [
                    3 => ['name' => 'Paul', 'age' => '25'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                ],
            ],
            [
                'name',
                Direction::Ascending,
                [
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                ],
            ],
            [
                'age',
                Direction::Descending,
                [
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                ],
            ],
            [
                'name',
                Direction::Descending,
                [
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                ],
            ],
        ];
    }

    #[DataProvider('provideSortMultipleColumnCases')]
    public function testSortMultipleColumn($params, $expected) : void
    {
        $statsTable = $this->_getSimpleTestData();
        $statsTable->sortByColumns($params);

        self::assertSame($expected, $statsTable->getData());
    }

    public static function provideSortMultipleColumnCases() : iterable
    {
        return [
            [
                [
                    'age' => Direction::Ascending,
                    'name' => Direction::Ascending,
                ], [
                    3 => ['name' => 'Paul', 'age' => '25'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                ],
            ], [
                [
                    'age' => Direction::Ascending,
                    'name' => Direction::Descending,
                ], [
                    3 => ['name' => 'Paul', 'age' => '25'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                ],
            ], [
                [
                    'age' => Direction::Descending,
                    'name' => Direction::Ascending,
                ], [
                    2 => ['name' => 'Jean', 'age' => '32'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                ],
            ], [
                [
                    'age' => Direction::Descending,
                    'name' => Direction::Descending,
                ], [
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                ],
            ],
        ];
    }

    #[DataProvider('provideSortMultipleColumnWithFuncCases')]
    public function testSortMultipleColumnWithFunc($params, $expected) : void
    {
        $statsTable = $this->_getAdvancedTestData();
        $statsTable->uSortByColumns($params);
        self::assertSame($expected, $statsTable->getData());
    }

    public static function provideSortMultipleColumnWithFuncCases() : iterable
    {
        $customSort = static function ($a, $b) : int {
            if ($a['nb'] === $b['nb']) {
                if ($a['id'] === $b['id']) {
                    return 0;
                }
                return $a['id'] < $b['id'] ? -1 : 1;
            }

            return $a['nb'] < $b['nb'] ? -1 : 1;
        };

        return [
            [
                ['order' => $customSort, 'name' => 'strcmp'],
                [
                    2 => ['name' => 'Jean', 'age' => '32', 'order' => ['nb' => 1, 'id' => '9210367']],
                    1 => ['name' => 'Jacques', 'age' => '28', 'order' => ['nb' => 10, 'id' => '2479109']],
                    0 => ['name' => 'Pierre', 'age' => '32', 'order' => ['nb' => 10, 'id' => '4587956']],
                    4 => ['name' => 'Celine', 'age' => '25', 'order' => ['nb' => 24, 'id' => '5214680']],
                    3 => ['name' => 'Paul', 'age' => '25', 'order' => ['nb' => 24, 'id' => '5214681']],
                ],
            ],
        ];
    }

    #[DataProvider('provideSortOneColumnWithFuncCases')]
    public function testSortOneColumnWithFunc(string $columnName, $customCompareFunc, $expected) : void
    {
        $statsTable = $this->_getAdvancedTestData();
        $statsTable->uSortByColumn($columnName, $customCompareFunc);

        // We have an egality for the last rows
        self::assertSame(
            array_slice($expected, 0, 3),
            array_slice($statsTable->getData(), 0, 3)
        );
    }

    public static function provideSortOneColumnWithFuncCases() : iterable
    {
        $customSort = static function ($a, $b) : int {
            if ($a === $b) {
                return 0;
            }

            return $a < $b ? 1 : -1;
        };

        return [
            [
                'age', $customSort,
                [
                    0 => ['name' => 'Pierre', 'age' => '32', 'order' => ['nb' => 10, 'id' => '4587956']],
                    2 => ['name' => 'Jean', 'age' => '32', 'order' => ['nb' => 1, 'id' => '9210367']],
                    1 => ['name' => 'Jacques', 'age' => '28', 'order' => ['nb' => 10, 'id' => '2479109']],
                    3 => ['name' => 'Paul', 'age' => '25', 'order' => ['nb' => 24, 'id' => '5214681']],
                    4 => ['name' => 'Celine', 'age' => '25', 'order' => ['nb' => 24, 'id' => '5214680']],
                ],
            ],
        ];
    }

    private function _getSimpleTestData() : StatsTable
    {
        return new StatsTable(
            [
                ['name' => 'Pierre', 'age' => '32'],
                ['name' => 'Jacques', 'age' => '28'],
                ['name' => 'Jean', 'age' => '32'],
                ['name' => 'Paul', 'age' => '25'],
            ],
            [
                'name' => 'Name',
                'age' => 'Age',
                'order' => 'Order',
            ]
        );
    }

    private function _getAdvancedTestData() : StatsTable
    {
        return new StatsTable(
            [
                ['name' => 'Pierre', 'age' => '32', 'order' => ['nb' => 10, 'id' => '4587956']],
                ['name' => 'Jacques', 'age' => '28', 'order' => ['nb' => 10, 'id' => '2479109']],
                ['name' => 'Jean', 'age' => '32', 'order' => ['nb' => 1, 'id' => '9210367']],
                ['name' => 'Paul', 'age' => '25', 'order' => ['nb' => 24, 'id' => '5214681']],
                ['name' => 'Celine', 'age' => '25', 'order' => ['nb' => 24, 'id' => '5214680']],
            ],
            [
                'name' => 'Name',
                'age' => 'Age',
                'order' => 'Order',
            ]
        );
    }
}
