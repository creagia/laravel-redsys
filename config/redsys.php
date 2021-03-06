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
     * Redsys order number should be unique. You can set the starting order number here if you need it.
     */
    'min_order_num' => env('REDSYS_MIN_ORDER_NUM', 0),
];
