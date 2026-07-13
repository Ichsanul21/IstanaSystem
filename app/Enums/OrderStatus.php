<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Process = 'process';
    case Finished = 'finished';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
