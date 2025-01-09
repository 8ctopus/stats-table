<?php

declare(strict_types=1);

namespace Tests\DynamicColumn;

use Oct8pus\StatsTable\DynamicColumn\CallbackColumnBuilder;
use Oct8pus\StatsTable\StatsTableBuilder;
use PHPUnit\Framework\TestCase;

class CallbackColumnBuilderTest extends TestCase
{
    public function test() : void
    {
        $statsTableBuilder = new StatsTableBuilder([
            ['hits' => 10, 'subscribers' => 5],
            ['hits' => 5, 'subscribers' => 2]
        ]);

        $callbackColumnBuilder = new CallbackColumnBuilder(static function($line) {
            return $line['hits'] * $line['subscribers'];
        });
        self::assertSame(
            [50, 10],
            $callbackColumnBuilder->buildColumnValues($statsTableBuilder)
        );
    }
}
