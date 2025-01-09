<?php

declare(strict_types=1);

namespace IgraalOSL\StatsTable\DynamicColumn;

use IgraalOSL\StatsTable\StatsTableBuilder;

interface DynamicColumnBuilderInterface
{
    public function buildColumnValues(StatsTableBuilder $statsTable);
}
