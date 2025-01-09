<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

interface DumperInterface
{
    /**
     * Dump the stats table
     * @param  StatsTable $statsTable The stats table to dump
     * @return string                 The stats table dumped
     */
    public function dump(StatsTable $statsTable) : string;

    /**
     * Retrieve mime-type
     * @return string
     */
    public function getMimeType() : string;
}
