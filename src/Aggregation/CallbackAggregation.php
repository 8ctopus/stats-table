<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Aggregation;

use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

class CallbackAggregation implements AggregationInterface
{
    private $callback;
    private readonly Format $format;

    public function __construct(callable $callback, Format $format = Format::Integer)
    {
        $this->callback = $callback;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable) : float
    {
        return ($this->callback)($statsTable);
    }

    public function getFormat() : Format
    {
        return $this->format;
    }
}
