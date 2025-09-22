<?php

declare(strict_types=1);

use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Aggregation\CountAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Direction;
use Oct8pus\StatsTable\Dumper\TextDumper;
use Oct8pus\StatsTable\DynamicColumn\CallbackColumnBuilder;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

require_once __DIR__ . '/vendor/autoload.php';

echo <<<'TXT'
1) example 1
2) example 2
3) example 3

Select example:
TXT . ' ';

$stdin = fopen('php://stdin', 'r');

if ($stdin === false) {
    throw new Exception('fopen');
}

$input = trim(fgets($stdin));

fclose($stdin);

echo "\n";

("example{$input}")();

function example1() : void
{
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
        'name' => new CountAggregation('name', Format::Integer),
        'age' => new AverageAggregation('age', Format::Integer),
        'weight' => new AverageAggregation('weight', Format::Integer),
        'height' => new AverageAggregation('height', Format::Float),
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
}

function example2() : void
{
    $data = [
        [
            'status' => 'active',
            'count' => 80,
        ], [
            'status' => 'cancelled',
            'count' => 20,
        ],
    ];

    $headers = [];

    $formats = [
        'status' => Format::String,
        'count' => Format::Integer,
    ];

    $aggregations = [
        'count' => new SumAggregation('count', Format::Integer),
    ];

    $builder = new StatsTableBuilder($data, $headers, $formats, $aggregations);

    // get count column total
    $total = $aggregations['count']->aggregate($builder);

    // add percentage column
    $dynamicColumn = new CallbackColumnBuilder(function (array $row) use ($total) : float {
        return $row['count'] / $total;
    });

    $table = $builder
        ->addDynamicColumn('percentage', $dynamicColumn, 'percentage', Format::Percent, new SumAggregation('percentage', Format::Percent))
        ->build();

    echo (new TextDumper())
        ->dump($table);
}

function example3() : void
{
    $data = [
        [
            'date' => '2025-01',
            'currency' => 'USD',
            'amount' => 80,
        ], [
            'date' => '2025-01',
            'currency' => 'USD',
            'amount' => 40,
        ], [
            'date' => '2025-01',
            'currency' => 'EUR',
            'amount' => 80,
        ], [
            'date' => '2025-01',
            'currency' => 'EUR',
            'amount' => 40,
        ], [
            'date' => '2024-12',
            'currency' => 'USD',
            'amount' => 80,
        ], [
            'date' => '2024-12',
            'currency' => 'USD',
            'amount' => 20,
        ], [
            'date' => '2024-12',
            'currency' => 'EUR',
            'amount' => 20,
        ], [
            'date' => '2024-12',
            'currency' => 'EUR',
            'amount' => 40,
        ],
    ];

    $headers = [];

    $formats = [
        'date' => Format::String,
        'currency' => Format::String,
        'amount' => Format::Integer,
    ];

    $aggregations = [];

    $builder = new StatsTableBuilder($data, $headers, $formats, $aggregations);

    $dynamicColumn = new CallbackColumnBuilder(function (array $row) : float {
        if ($row['currency'] === 'USD') {
            return $row['amount'];
        }

        $EURtoUSD = 1.0295998;
        return $row['amount'] * $EURtoUSD;
    });

    $builder->addDynamicColumn('consolidated', $dynamicColumn, 'consolidated', Format::Integer, new SumAggregation('consolidated', Format::Integer));

    $dumper = new TextDumper();

    $table = $builder
        ->groupBy(['date'], ['currency', 'amount'])
        ->build();

    echo $dumper->dump($table);
}
