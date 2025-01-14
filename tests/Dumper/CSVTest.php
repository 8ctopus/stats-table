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

        $table = new StatsTable([
                ['date' => '2014-01-01'],
                ['date' => new DateTime('2014-01-01')],
            ],
            [],
            [], [
                'date' => Format::Date
            ]);

        self::assertSame(
            <<<TXT
            2014-01-01
            2014-01-01

            TXT,
            $csvDumper->dump($table)
        );

        self::assertSame(
            <<<TXT
            "2014-01-01 00:00:00"
            "2014-01-01 00:00:00"

            TXT,
            $csvDumper->dump(new StatsTable([['date' => '2014-01-01 00:00:00'], ['date' => new DateTimeImmutable('2014-01-01 00:00:00')]], [], [], ['date' => Format::DateTime]))
        );

        self::assertSame(
            <<<TXT
            132
            133

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.3]], [], [], ['test' => Format::Integer]))
        );

        self::assertSame(
            <<<TXT
            132.00
            133.35

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Float]))
        );

        self::assertSame(
            <<<TXT
            "132 €"
            "133 €"

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Money]))
        );

        self::assertSame(
            <<<TXT
            "132.00 €"
            "133.35 €"

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::Money2]))
        );

        self::assertSame(
            <<<TXT
            132%
            133%

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 1.32], ['test' => 1.33]], [], [], ['test' => Format::Percent]))
        );

        self::assertSame(
            <<<TXT
            132.00%
            133.35%

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 1.32], ['test' => 1.3335]], [], [], ['test' => Format::Percent2]))
        );

        self::assertSame(
            <<<TXT
            132
            133.351

            TXT,
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::String]))
        );

        $csvDumper->enableAggregation(true);
        $csvDumper->enableHeaders(true);

        self::assertSame(
            <<<TXT
            Date,Hits
            2014-01-01,3
            Total,3

            TXT,
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
