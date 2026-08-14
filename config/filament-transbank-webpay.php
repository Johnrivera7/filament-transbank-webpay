<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default credentials (single-tenant / .env)
    |--------------------------------------------------------------------------
    | For multi-tenant apps, ignore these and resolve credentials via
    | FilamentTransbankWebpayPlugin::credentialsUsing(...).
    */
    'enabled' => env('TRANSBANK_ENABLED', true),
    'commerce_code' => env('TRANSBANK_COMMERCE_CODE'),
    'api_key' => env('TRANSBANK_API_KEY'),
    'environment' => env('TRANSBANK_ENVIRONMENT', 'integration'), // integration|production

    /*
    |--------------------------------------------------------------------------
    | Filament navigation
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'group' => 'Pagos',
        'sort' => 40,
        'icon' => 'heroicon-o-credit-card',
        'label' => 'Transbank Webpay',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature flags
    |--------------------------------------------------------------------------
    */
    'register_settings_page' => true,
];
