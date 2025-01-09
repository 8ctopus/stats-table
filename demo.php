<?php

declare(strict_types=1);

use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\Dumper\HTML\HTMLDumper;
use Oct8pus\StatsTable\Dumper\Text\TextDumper;
use Oct8pus\StatsTable\StatsTable;

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
