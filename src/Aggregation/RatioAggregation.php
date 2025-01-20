<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

class RatioAggregation implements AggregationInterface
{
    private readonly string $numeratorColumn;
    private readonly string $denominatorColumn;
    private readonly Format $format;

    public function __construct(string $denominatorColumn, string $numeratorColumn, Format $format = Format::Percent2)
    {
        $this->denominatorColumn = $denominatorColumn;
        $this->numeratorColumn = $numeratorColumn;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : float
    {
        $sumNumerator = new SumAggregation($this->numeratorColumn);
        $sumDenominator = new SumAggregation($this->denominatorColumn);

        $numerator = $sumNumerator->aggregate($statsTable);
        $denominator = $sumDenominator->aggregate($statsTable);

        return $denominator ? $numerator / $denominator : $numerator;
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
