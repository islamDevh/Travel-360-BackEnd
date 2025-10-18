<?php

return [
    'merchant_email' => env('PAYTABS_MERCHANT_EMAIL', ''),
    'server_key'     => env('paytabs_server_key', ''),
    'currency'       => env('PAYTABS_CURRENCY', 'USD'),
    'language'       => env('PAYTABS_LANGUAGE', 'en'),
    'profile_id'     => env('paytabs_profile_id', ''),
    'region'         => env('paytabs_region', 'SAU'),
    'return_url'     => env('PAYTABS_RETURN_URL', ''),
    'callback_url'   => env('PAYTABS_CALLBACK_URL', ''),
];
