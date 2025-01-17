# stats-table

[![packagist](https://poser.pugx.org/8ctopus/stats-table/v)](https://packagist.org/packages/8ctopus/stats-table)
[![downloads](https://poser.pugx.org/8ctopus/stats-table/downloads)](https://packagist.org/packages/8ctopus/stats-table)
[![min php version](https://poser.pugx.org/8ctopus/stats-table/require/php)](https://packagist.org/packages/8ctopus/stats-table)
[![license](https://poser.pugx.org/8ctopus/stats-table/license)](https://packagist.org/packages/8ctopus/stats-table)
[![tests](https://github.com/8ctopus/stats-table/actions/workflows/tests.yml/badge.svg)](https://github.com/8ctopus/stats-table/actions/workflows/tests.yml)
![code coverage badge](https://raw.githubusercontent.com/8ctopus/stats-table/image-data/coverage.svg)
![lines of code](https://raw.githubusercontent.com/8ctopus/stats-table/image-data/lines.svg)

Create statistics tables and export them to text, JSON, CSV or Excel.

## introduction

This package facilitates the creation of statistical tables from datasets. It provides features including data aggregation (sum, count, average), dynamic column calculations, data grouping and sorting. The generated tables can be exported to text, JSON, CSV, or Excel.

This package is a fork of [paxal/stats-table](https://github.com/paxal/stats-table). Migrating from the parent package shouldn't be too hard, but except a bit of work.

## installation

    composer require 8ctopus/stats-table

## usage

The `StatsTableBuilder` class helps combine data from multiple tables, build aggregations (column sum, count, average, ...), create calculated columns, and add grouping. While the second class `StatsTable` allows to sort the table and remove columns.

### example 1

Here's an example which shows aggregation, dynamic column and table sorting. Play with this example in `demo.php`.

```php
use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Aggregation\CountAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Direction;
use Oct8pus\StatsTable\Dumper\TextDumper;
use Oct8pus\StatsTable\DynamicColumn\CallbackColumnBuilder;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

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

// add body mass index row to table
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
```

```txt
     Name  Age  Weight  Height    BMI
     Paul   25   75.00    1.82  22.64
  Jacques   28   60.00    1.67  21.51
   Pierre   32  100.00    1.87  28.60
     Jean   32   80.00    1.98  20.41
        4   29      78    1.84  23.29
```

### example 2

Here's another example with a dynamic column which depends on the aggregation result. This is useful when your data comes from a database request as it drastically simplifies the complexity of the query.

```php
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
    new SumAggregation('count', Format::Integer),
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
```

```txt
     status  count  percentage
     active     80         80%
  cancelled     20         20%
               100        100%
```

### example 3

Here's another example demonstrating consolidated revenue and group by date.

```php
<?php

use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Dumper\TextDumper;
use Oct8pus\StatsTable\DynamicColumn\CallbackColumnBuilder;
use Oct8pus\StatsTable\Format;
use Oct8pus\StatsTable\StatsTableBuilder;

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

$aggregations = [
    'amount' => new SumAggregation('amount', Format::Integer),
];

$builder = new StatsTableBuilder($data, $headers, $formats, $aggregations);

// dynamic column with consolidated revenue in USD
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
```

```txt
     date  consolidated
  2025-01           243
  2024-12           161
                    405
```
