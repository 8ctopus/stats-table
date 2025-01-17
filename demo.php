<?php

declare(strict_types=1);

use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Aggregation\CountAggregation;
use Oct8pus\StatsTable\Direction;
use Oct8pus\StatsTable\Dumper\TextDumper;
use Oct8pus\StatsTable\DynamicColumn\CallbackColumnBuilder;
use Oct8pus\StatsTable\Format;
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
    'name' => Format::String,
    'age' => Format::Integer,
    'weight' => Format::Float,
    'height' => Format::Float,
];

$aggregations = [
    new CountAggregation('name', Format::Integer),
    new AverageAggregation('age', Format::Integer),
    new AverageAggregation('weight', Format::Integer),
    new AverageAggregation('height', Format::Float),
];

$builder = new StatsTableBuilder($data, $headers, $formats, $aggregations);

$dynamicColumn = new CallbackColumnBuilder(function (array $row) : float {
    return $row['weight'] / ($row['height'] * $row['height']);
});

$table = $builder
    ->addDynamicColumn('BMI', $dynamicColumn, 'BMI', Format::Float, new AverageAggregation('BMI', Format::Float))
    ->build();

$table->sortByColumns([
    'age' => Direction::Ascending,
    'height' => Direction::Ascending,
]);

$dumper = new TextDumper();
echo $dumper->dump($table);
