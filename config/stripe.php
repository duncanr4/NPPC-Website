<?php

return [
    'pk'                    => env('STRIPE_PK', ''),
    'sk'                    => env('STRIPE_SK', ''),
    'donation_product_id'   => env('STRIPE_DONATION_PRODUCT_ID', ''),
    'donation_price_id'     => env('STRIPE_DONATION_PRICE_ID', ''),
    'donation_name'         => 'NPPC Donation',
];
