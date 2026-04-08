<?php

declare(strict_types=1);

namespace App\Enum;

enum EventCategory: string
{
    case ADULTS = 'adults';
    case KIDS = 'kids';
}
