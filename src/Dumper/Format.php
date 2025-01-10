<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Dumper;

enum Format : string
{
    case fDate = 'date';
    case fDateTime = 'datetime';
    case fFloat = 'float2';
    case fInteger = 'integer';
    case fMoney = 'money';
    case fMoney2 = 'money2';
    case fPercent = 'percent';
    case fPercent2 = 'percent2';
    case fString = 'string';
    case fLink = 'link';
}
