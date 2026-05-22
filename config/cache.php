<?php

return [
    'default' => env('CACHE_STORE', 'database'),
    'stores' => [
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
    ],
    'prefix' => env('CACHE_PREFIX', 'telecomos_cache'),
];
