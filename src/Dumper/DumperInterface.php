<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

interface DumperInterface
{
    public function __construct(array $options = []);

    /**
     * Dump table
     *
     * @param StatsTable $statsTable
     *
     * @return array|string
     */
    public function dump(StatsTable $statsTable) : array|string;
}
