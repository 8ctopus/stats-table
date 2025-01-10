<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable;

enum Format : string
{
    case Date = 'date';
    case DateTime = 'datetime';
    case Float = 'float2';
    case Integer = 'integer';
    case Money = 'money';
    case Money2 = 'money2';
    case Percent = 'percent';
    case Percent2 = 'percent2';
    case String = 'string';
    case Link = 'link';
}
