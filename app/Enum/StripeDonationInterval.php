<?php

namespace App\Enum;

enum StripeDonationInterval: string {
    case OneTime = 'one_time';
    case Monthly = 'month';
    case Yearly  = 'year';
}
