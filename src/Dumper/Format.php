<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

enum Format : string
{
    case DATE = 'date';
    case DATETIME = 'datetime';
    case FLOAT2 = 'float2';
    case INTEGER = 'integer';
    case MONEY = 'money';
    case MONEY2 = 'money2';
    case PCT = 'percent';
    case PCT2 = 'percent2';
    case STRING = 'string';
    case LINK = 'link';
}
