<?php

return [
    /**
     * Used to define the service URL. Possible values 'test', 'production' or 'local'.
     *
     * It's recommended to use 'local' during your development to enable a local gateway to test your
     * application without need to expose it.
     */
    'environment' => env('REDSYS_ENVIRONMENT'),

    /**
     * Values sent to Redsys.
     */
    'tpv' => [
        'terminal' => env('REDSYS_TERMINAL', 1),
        'currency' => \Creagia\Redsys\Enums\Currency::EUR,
        'merchantCode' => env('REDSYS_MERCHANT_CODE'), // Default test code: 999008881
        'key' => env('REDSYS_KEY'), // Default test key: sq7HjrUOBfKmC576ILgskD5srU870gJ7
    ],

    /**
     * Prefix used by the package routes. 'redsys' by default.
     */
    'routes_prefix' => env('REDSYS_ROUTE_PREFIX', 'redsys'),

    /**
     * Route names for successful and unsuccessful pages. Redsys redirects to these routes
     * after the payment is finished. By default, this package provides two neutral views.
     */
    'successful_payment_route_name' => env('REDSYS_SUCCESSFUL_ROUTE_NAME', null),
    'unsuccessful_payment_route_name' => env('REDSYS_UNSUCCESSFUL_ROUTE_NAME', null),

    /**
     * Use an automatic prefix for the order number with the current year and month.
     */
    'order_num_auto_prefix' => true,

    /**
     * Redsys order number should be unique. Here you can set an order number prefix if you need it.
     * This prefix must be an integer number.
     */
    'order_num_prefix' => env('REDSYS_ORDER_NUM_PREFIX', 0),
];
