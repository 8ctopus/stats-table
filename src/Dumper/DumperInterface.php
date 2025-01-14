<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

interface DumperInterface
{
    public function __construct(array $options = []);

    public function dump(StatsTable $statsTable);
}
