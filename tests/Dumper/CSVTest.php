<?php

declare(strict_types=1);

namespace Tests\Dumper;

use DateTime;
use DateTimeImmutable;
use Oct8pus\StatsTable\Dumper\CSVDumper;
use Oct8pus\StatsTable\Dumper\DumperInterface;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTable;

class CSVTest extends DumperTestAbstract
{
    public function testFormats() : void
    {
        $csvDumper = new CSVDumper();
        $csvDumper->enableHeaders(false);
        $csvDumper->enableAggregation(false);

        // DATE
        self::assertSame(
            "2014-01-01\n2014-01-01\n",
            $csvDumper->dump(new StatsTable([['date' => '2014-01-01'], ['date' => new DateTime('2014-01-01')]], [], [], ['date' => Format::Date]))
        );

        // DATETIME
        self::assertSame(
            "\"2014-01-01 00:00:00\"\n\"2014-01-01 00:00:00\"\n",
            $csvDumper->dump(new StatsTable([['date' => '2014-01-01 00:00:00'], ['date' => new DateTimeImmutable('2014-01-01 00:00:00')]], [], [], ['date' => Format::DateTime]))
        );

        // INTEGER
        self::assertSame(
            "132\n133\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.3]], [], [], ['test' => Format::Integer]))
        );

        // FLOAT2
        self::assertSame(
            "132.00\n133.35\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Float]))
        );

        // MONEY
        self::assertSame(
            "\"132 €\"\n\"133 €\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Money]))
        );

        // MONEY2
        self::assertSame(
            "\"132.00 €\"\n\"133.35 €\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Money2]))
        );

        // PCT
        self::assertSame(
            "\"132 %\"\n\"133 %\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Percent]))
        );

        // PCT2
        self::assertSame(
            "\"132.00 %\"\n\"133.35 %\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Percent2]))
        );

        // String
        self::assertSame(
            "132\n133.351\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::String]))
        );

        $csvDumper->enableAggregation(true);
        $csvDumper->enableHeaders(true);
        self::assertSame(
            "Date,Hits\n2014-01-01,3\nTotal,3\n",
            $csvDumper->dump(new StatsTable(
                [['date' => '2014-01-01', 'hits' => 3]],
                ['date' => 'Date', 'hits' => 'Hits'],
                ['date' => 'Total', 'hits' => 3]
            ))
        );
    }

    protected function getDumper() : DumperInterface
    {
        return new CSVDumper();
    }
}
