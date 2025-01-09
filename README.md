# stats-table

[![packagist](https://poser.pugx.org/8ctopus/stats-table/v)](https://packagist.org/packages/8ctopus/stats-table)
[![downloads](https://poser.pugx.org/8ctopus/stats-table/downloads)](https://packagist.org/packages/8ctopus/stats-table)
[![min php version](https://poser.pugx.org/8ctopus/stats-table/require/php)](https://packagist.org/packages/8ctopus/stats-table)
[![license](https://poser.pugx.org/8ctopus/stats-table/license)](https://packagist.org/packages/8ctopus/stats-table)
[![tests](https://github.com/8ctopus/stats-table/actions/workflows/tests.yml/badge.svg)](https://github.com/8ctopus/stats-table/actions/workflows/tests.yml)
![code coverage badge](https://raw.githubusercontent.com/8ctopus/stats-table/image-data/coverage.svg)
![lines of code](https://raw.githubusercontent.com/8ctopus/stats-table/image-data/lines.svg)

Create statistic tables and export them to text, JSON, CSV or Excel.

This package is a fork of [paxal/stats-table](https://github.com/paxal/stats-table)

## Installation

    composer require 8ctopus/stats-table

## Usage

```php
use Oct8pus\StatsTable\StatsTable;

$data = [
    ['date' => '2014-01-01', 'hits' => 32500],
    ['date' => '2014-01-02', 'hits' => 48650],
];

$headers = [
    'date' => 'Date',
    'hits' => 'Number of hits'
];

$table = new StatsTable($data, $headers);

$dumper = new TXTDumper();
echo $dumper->dump($table);
```

```txt
Date       Number of hits
 2014-01-01          32500
 2014-01-02          48650
```

### Table builder

The `StatsTableBuilder` class helps combine data from multiple tables, create automatic calculated columns, and build aggregations (sum, count, average, ...).

```php
use Oct8pus\StatsTable\Aggregation\AverageAggregation;
use Oct8pus\StatsTable\Aggregation\SumAggregation;
use Oct8pus\StatsTable\Dumper\TXT\TXTDumper;
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
    'name' => Format::STRING,
    'age' => Format::INTEGER,
    'weight' => Format::FLOAT2,
    'height' => Format::FLOAT2,
];

$aggregations = [
    'age' => new SumAggregation('age', Format::INTEGER),
    'height' => new AverageAggregation('height', Format::FLOAT2),
];

$aggregationsFormats = [
    'age' => Format::INTEGER,
    'height' => Format::FLOAT2,
];

$builder = new StatsTableBuilder($data, $headers, $formats, $aggregations);

$dynamicColumn = new CallbackColumnBuilder(function($row) : float {
    return $row['weight'] / ($row['height'] * $row['height']);
});

$builder->addDynamicColumn('BMI', $dynamicColumn, 'BMI', Format::FLOAT2);

$table = $builder->build();

$table->sortMultipleColumn([
    'age' => true,
    'height' => true,
]);

$dumper = new TXTDumper();
echo $dumper->dump($table);
```

```txt
Name    Age Weight Height BMI
    Paul  25     75   1,82 22,64
 Jacques  28     60   1,67 21,51
  Pierre  32    100   1,87 28,60
    Jean  32     80   1,98 20,41
         117         1.835
```
