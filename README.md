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

The `stats-table` project facilitates the creation of statistical tables from datasets. It provides features including data aggregation (sum, count, average), dynamic column calculations, and data sorting. The generated tables can be exported to text, JSON, CSV, or Excel. Streamline the process of building and exporting comprehensive statistical tables, enhancing data analysis and reporting capabilities.

This package is a fork of [paxal/stats-table](https://github.com/paxal/stats-table). Migrating from the parent package shouldn't be too hard, but except a bit of work.

## installation

    composer require 8ctopus/stats-table

## usage

The `StatsTableBuilder` class helps combine data from multiple tables, build aggregations (sum, count, average, ...), and create automatic calculated columns.

```php
use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;
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
    'age' => new SumAggregation('age', Format::Integer),
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
```

```txt
     Name  Age  Weight  Height    BMI
     Paul   25   75.00    1.82  22.64
  Jacques   28   60.00    1.67  21.51
   Pierre   32  100.00    1.87  28.60
     Jean   32   80.00    1.98  20.41
           117            1.84  23.29
```
