<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\DynamicColumn;

use Oct8pus\StatsTable\StatsTableBuilder;

interface DynamicColumnBuilderInterface
{
    public function buildColumnValues(StatsTableBuilder $statsTable) : array;
}
