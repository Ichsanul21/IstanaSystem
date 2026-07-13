<?php

return [
    'points_per_amount' => env('LOYALTY_POINTS_PER_AMOUNT', 1000),
    'points_per_unit' => env('LOYALTY_POINTS_PER_UNIT', 1),
    'redeem_rate' => env('LOYALTY_REDEEM_RATE', 100),
    'points_worth' => env('LOYALTY_POINTS_WORTH', 1000),
    'expiry_days' => env('LOYALTY_EXPIRY_DAYS', 90),
];
