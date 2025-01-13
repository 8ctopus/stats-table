<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

use Oct8pus\StatsTable\StatsTable;

interface DumperInterface
{
    /**
     * Dump table
     *
     * @param StatsTable $statsTable
     *
     * @return string|array
     */
    public function dump(StatsTable $statsTable) : string|array;

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType() : string;
}
