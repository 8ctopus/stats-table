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

The `StatsTable` class holds the data. It takes one mandatory argument, and 4 optional arguments. The simpler way to create a new table is to pass the data itself and its headers (headers are optional).

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

$statsTable = new StatsTable($data, $headers);
```

### Dumping a table

Four formats are currently supported : text, Excel, CSV and JSON. Thus, you can use the same table with your ajax calls or to be downloaded.

```php
use Oct8pus\StatsTable\Dumper\Excel\ExcelDumper;

$excelDumper = new ExcelDumper();
$excelContents = $excelDumper->dump($statsTable);

header('Content-type: application/vnd.ms-excel');
echo $excelContents;
```

### Using stats table builder

To help you construct a table, you can use the `StatsTableBuilder` class.\

It helps you combine data from multiple tables, and can create automatic calculated columns. It also helps build aggregations (aka the footer line), with multiple possibilities : ratio, sum, average or static content.

```php
use Oct8pus\StatsTable\StatsTableBuilder;

$statsTableBuilder = new StatsTableBuilder([
        '2014-01-01' => ['hits' => 32500],
        '2014-01-02' => ['hits' => 48650],
    ],
    [
        'hits' => 'Number of hits',
    ]
);

$statsTableBuilder->addIndexesAsColumn('date', 'Date');

$statsTable = $statsTableBuilder->build();
```

#### Advanced example with aggregation, dynamic column multiple column sorting

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

$dumper = new HTMLDumper();
echo $dumper->dump($table);
```
