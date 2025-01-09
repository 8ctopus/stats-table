<?php

declare(strict_types=1);

use IgraalOSL\StatsTable\Aggregation\SumAggregation;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\Dumper\HTML\HTMLDumper;
use IgraalOSL\StatsTable\Dumper\JSON\JSONDumper;
use IgraalOSL\StatsTable\Dumper\Text\TextDumper;
use IgraalOSL\StatsTable\StatsTable;

require_once __DIR__ . '/vendor/autoload.php';

$data = [
    [
        'name' => 'Pierre',
        'age' => '32',
    ], [
        'name' => 'Jacques',
        'age' => '28',
    ], [
        'name' => 'Jean',
        'age' => '32',
    ], [
        'name' => 'Paul',
        'age' => '25',
    ],
];

$headers = [
    'name' => 'Name',
    'age' => 'Age',
];

$formats = [
    'age' => Format::INTEGER,
];

$aggregations = [
    'age' => new SumAggregation('age', Format::INTEGER),
];

$statsTable = new StatsTable($data, $headers, $formats, $aggregations);

//$dumper = new TextDumper();
$dumper = new HTMLDumper();
echo $dumper->dump($statsTable);
