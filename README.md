***This package is a fork of [igraal/stats-table](https://github.com/igraal/stats-table)***

stats-table
===========

PHP Libary to handle statistics tables and CSV, JSON and Excel exports. [![Build status](https://github.com/paxal/stats-table/actions/workflows/main.yml/badge.svg)](https://github.com/paxal/stats-table/actions/workflows/main.yml)

Summary
-------

This library helps you create statistical tables given some data. You provide data, headers and what you want for the footer line, and then you can dump your table into a JSON, CSV or Excel file.

This is very useful to manipulate a lot of tables you want to see in an HTML FrontOffice and when you want to add the ability to get this data in CSV or Excel File as well.

Installation
------------

### Using composer

Using composer, just run the following to require the latest stable :

```bash
composer req paxal/stats-table
```

Usage
-----

### Using the class StatsTable

The class `StatsTable` is the class that will hold your data. It takes one mandatory argument, and 4 optional arguments. The simpler way to create a new table is to pass the data itself and its headers (even if headers are optional).

```php
use IgraalOSL\StatsTable\StatsTable;

$data = [
    ['date' => '2014-01-01', 'hits' => 32500],
    ['date' => '2014-01-02', 'hits' => 48650],
];
$headers = ['date' => 'Date', 'hits' => 'Number of hits'];
$statsTable = new StatsTable($data, $headers);
```

### Dumping a table

Three formats are currently supported : Excel, CSV and JSON. Thus, you can use the same table with your ajax calls or to be downloaded.

First, create your dumper, then dump your data.

```php
use IgraalOSL\StatsTable\Dumper\Excel\ExcelDumper;

$excelDumper = new ExcelDumper();
$excelContents = $excelDumper->dump($statsTable);

header('Content-type: application/vnd.ms-excel');
echo $excelContents;
```

### Using stats table builder

To help you construct a table, you can use the `StatsTableBuilder` class. It helps you combine data from multiple tables, and can create automatic calculated columns. It also helps you build aggregations (aka the footer line), with multiple possibilities : ratio, sum, average or static content.

```php
use IgraalOSL\StatsTable\StatsTableBuilder;

$data = [
    '2014-01-01' => ['hits' => 32500],
    '2014-01-02' => ['hits' => 48650],
];

$statsTableBuilder = new StatsTableBuilder(
    $data,
    ['hits' => 'Number of hits']
);
$statsTableBuilder->addIndexesAsColumn('date', 'Date');

$statsTable = $statsTableBuilder->build();
```

#### Advanced example with aggregation, dynamic column multiple column sorting

```php
use IgraalOSL\StatsTable\Aggregation\AverageAggregation;
use IgraalOSL\StatsTable\Aggregation\SumAggregation;
use IgraalOSL\StatsTable\Dumper\TXT\TXTDumper;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\DynamicColumn\CallbackColumnBuilder;
use IgraalOSL\StatsTable\StatsTableBuilder;

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
