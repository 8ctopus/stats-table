<?php

declare(strict_types=1);

use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Dumper\TXTDumper;
use Oct8pus\StatsTable\Dumper\Format;
use Oct8pus\StatsTable\DynamicColumn\CallbackColumnBuilder;
use Oct8pus\StatsTable\StatsTableBuilder;

require_once __DIR__ . '/vendor/autoload.php';

$data = [
    [
        'name' => 'Pierre',
        'age' => 32,
        'weight' => 100,
        'height' => 1.87,
    ], [
        'name' => 'Jacques',
        'age' => 28,
        'weight' => 60,
        'height' => 1.67,
    ], [
        'name' => 'Jean',
        'age' => 32,
        'weight' => 80,
        'height' => 1.98,
    ], [
        'name' => 'Paul',
        'age' => 25,
        'weight' => 75,
        'height' => 1.82,
    ],
];

$headers = [
    'name' => 'Name',
    'age' => 'Age',
    'weight' => 'Weight',
    'height' => 'Height',
];

$formats = [
    'name' => Format::fString,
    'age' => Format::fInteger,
    'weight' => Format::fFloat,
    'height' => Format::fFloat,
];

$aggregations = [
    'age' => new SumAggregation('age', Format::fInteger),
    'height' => new AverageAggregation('height', Format::fFloat),
];

$aggregationsFormats = [
    'age' => Format::fInteger,
    'height' => Format::fFloat,
];

$builder = new StatsTableBuilder($data, $headers, $formats, $aggregations);

$dynamicColumn = new CallbackColumnBuilder(function($row) : float {
    return $row['weight'] / ($row['height'] * $row['height']);
});

$table = $builder
    ->addDynamicColumn('BMI', $dynamicColumn, 'BMI', Format::fFloat)
    ->build();

$table->sortByColumns([
    'age' => true,
    'height' => true,
]);

$dumper = new TXTDumper();
echo $dumper->dump($table);
