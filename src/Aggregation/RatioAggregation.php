<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

class RatioAggregation implements AggregationInterface
{
    private $valueInternalName;
    private $overInternalName;
    private Format $format;

    public function __construct($overInternalName, $valueInternalName, Format $format = Format::PCT2)
    {
        $this->valueInternalName = $valueInternalName;
        $this->overInternalName = $overInternalName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : mixed
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
