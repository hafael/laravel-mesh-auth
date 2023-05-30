<?php

return [

    /**
     * API key model
     */
    'model' => \Hafael\Mesh\Auth\Models\AppAccessToken::class,

    'api_key' => env('API_KEY'),

    /**
     * API Gateway service key
     */
    'shared_key' => env('APP_SHARED_KEY'),
    'shared_secret' => env('APP_SHARED_SECRET'),

    /**
     * Key identifiers
     */
    'key_names' => [
        'params' => [
            'key' => 'api_key',
            'secret' => 'api_secret',
        ],
        'headers' => [
            'key' => 'X-API-KEY',
            'secret' => 'X-API-SECRET',
        ]
    ],

];
