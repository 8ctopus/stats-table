<?php

declare(strict_types=1);

namespace Tests;

use IgraalOSL\StatsTable\StatsTable;
use PHPUnit\Framework\TestCase;

class StatsTableTest extends TestCase
{
    public function testRemoveColumn() : void
    {
        $statsTable = new StatsTable(
            [
                ['a' => 'a', 'b' => 'b'],
                ['a' => 'A', 'b' => 'B']
            ],
            ['a' => 'Alpha', 'b' => 'Bravo']
        );

        $statsTable->removeColumn('b');

        self::assertSame(['a' => 'Alpha'], $statsTable->getHeaders());

        self::assertSame(
            [
                ['a' => 'a'],
                ['a' => 'A']
            ],
            $statsTable->getData()
        );
    }

    private function _getSimpleTestData()
    {
        return new StatsTable(
            [
                ['name' => 'Pierre', 'age' => '32'],
                ['name' => 'Jacques', 'age' => '28'],
                ['name' => 'Jean', 'age' => '32'],
                ['name' => 'Paul', 'age' => '25'],
            ],
            ['name' => 'Name', 'age' => 'Age', 'order' => 'Order']
        );
    }


    #[\PHPUnit\Framework\Attributes\DataProvider('dataProviderForOneColumn')]
    public function testSortOneColumn($columnName, $asc, $expected) : void
    {
        $statsTable = $this->_getSimpleTestData();
        $statsTable->sortColumn($columnName, $asc);
        self::assertSame($expected, $statsTable->getData());
    }


    static public function dataProviderForOneColumn()
    {
        return [
            [
                'age',
                true,
                [
                    3 => ['name' => 'Paul', 'age' => '25'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                ]
            ],
            [
                'name',
                true,
                [
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                ]
            ],
            [
                'age',
                false,
                [
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                ]
            ],
            [
                'name',
                false,
                [
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                ]
            ]
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dataProviderForMultipleColumn')]
    public function testSortMultipleColumn($params, $expected) : void
    {
        $statsTable = $this->_getSimpleTestData();
        $statsTable->sortMultipleColumn($params);
        self::assertSame($expected, $statsTable->getData());
    }

    static public function dataProviderForMultipleColumn()
    {
        return [
            [
                ['age' => true,'name' => true],
                [
                    3 => ['name' => 'Paul', 'age' => '25'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                ]
            ],
            [
                ['age' => true,'name' => false],
                [
                    3 => ['name' => 'Paul', 'age' => '25'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                ]
            ],
            [
                ['age' => false,'name' => true],
                [
                    2 => ['name' => 'Jean', 'age' => '32'],
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                ]
            ],
            [
                ['age' => false,'name' => false],
                [
                    0 => ['name' => 'Pierre', 'age' => '32'],
                    2 => ['name' => 'Jean', 'age' => '32'],
                    1 => ['name' => 'Jacques', 'age' => '28'],
                    3 => ['name' => 'Paul', 'age' => '25'],
                ]
            ]
        ];
    }


    private function _getAdvancedTestData()
    {
        return new StatsTable(
            [
                ['name' => 'Pierre', 'age' => '32', 'order' => ['nb' => 10,'id' => '4587956']],
                ['name' => 'Jacques', 'age' => '28', 'order' => ['nb' => 10,'id' => '2479109']],
                ['name' => 'Jean', 'age' => '32', 'order' => ['nb' => 1,'id' => '9210367']],
                ['name' => 'Paul', 'age' => '25', 'order' => ['nb' => 24,'id' => '5214681']],
                ['name' => 'Celine', 'age' => '25', 'order' => ['nb' => 24,'id' => '5214680']],
            ],
            ['name' => 'Name', 'age' => 'Age', 'order' => 'Order']
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dataProviderForMultipleColumnWithFunc')]
    public function testSortMultipleColumnWithFunc($params, $expected) : void
    {
        $statsTable = $this->_getAdvancedTestData();
        $statsTable->uSortMultipleColumn($params);
        self::assertSame($expected, $statsTable->getData());
    }

    static public function dataProviderForMultipleColumnWithFunc()
    {
        $customSort = static function($a, $b){
            if($a['nb'] === $b['nb']) {
                if($a['id'] === $b['id']) {
                    return 0;
                }
                return $a['id'] < $b['id']  ? -1 :1;
            }

            return $a['nb'] < $b['nb']  ? -1 :1;
        };


        return [
            [
                ['order' => $customSort, 'name' => 'strcmp'],
                [
                    2 => ['name' => 'Jean', 'age' => '32', 'order' => ['nb' => 1,'id' => '9210367']],
                    1 => ['name' => 'Jacques', 'age' => '28', 'order' => ['nb' => 10,'id' => '2479109']],
                    0 => ['name' => 'Pierre', 'age' => '32', 'order' => ['nb' => 10,'id' => '4587956']],
                    4 => ['name' => 'Celine', 'age' => '25', 'order' => ['nb' => 24,'id' => '5214680']],
                    3 => ['name' => 'Paul', 'age' => '25', 'order' => ['nb' => 24,'id' => '5214681']],
                ]
            ]
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dataProviderForOneColumnWithFunc')]
    public function testSortOneColumnWithFunc($columnName, $customCompareFunc, $expected) : void
    {
        $statsTable = $this->_getAdvancedTestData();
        $statsTable->uSortColumn($columnName, $customCompareFunc);

        // We have an egality for the last rows
        self::assertSame(
            array_slice($expected, 0, 3),
            array_slice($statsTable->getData(), 0, 3)
        );
    }

    static public function dataProviderForOneColumnWithFunc()
    {
        $customSort = static function($a, $b){
            if($a === $b) {
               return 0;
            }

            return $a < $b  ? 1 : -1;
        };

        return [
            [
                'age', $customSort,
                [
                    0 => ['name' => 'Pierre', 'age' => '32', 'order' => ['nb' => 10,'id' => '4587956']],
                    2 => ['name' => 'Jean', 'age' => '32', 'order' => ['nb' => 1,'id' => '9210367']],
                    1 => ['name' => 'Jacques', 'age' => '28', 'order' => ['nb' => 10,'id' => '2479109']],
                    3 => ['name' => 'Paul', 'age' => '25', 'order' => ['nb' => 24,'id' => '5214681']],
                    4 => ['name' => 'Celine', 'age' => '25', 'order' => ['nb' => 24,'id' => '5214680']],
                ]
            ]
        ];
    }
}
