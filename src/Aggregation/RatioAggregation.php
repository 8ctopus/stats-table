<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

class RatioAggregation implements AggregationInterface
{
    private readonly string $valueInternalName;
    private readonly string $overInternalName;
    private readonly Format $format;

    public function __construct(string $overInternalName, string $valueInternalName, Format $format = Format::Percent2)
    {
        $this->valueInternalName = $valueInternalName;
        $this->overInternalName = $overInternalName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : float
    {
        $sumValueAggregation = new SumAggregation($this->valueInternalName);
        $sumOverAggregation = new SumAggregation($this->overInternalName);

        $sumValue = $sumValueAggregation->aggregate($statsTable);
        $sumOver = $sumOverAggregation->aggregate($statsTable);

        return $sumOver ? $sumValue / $sumOver : $sumValue;
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
