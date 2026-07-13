<?php

namespace App\Enums;

enum TaxRegime: string
{
    case None = 'none';
    case Pp23 = 'pp23';
    case Pkp = 'pkp';
}
