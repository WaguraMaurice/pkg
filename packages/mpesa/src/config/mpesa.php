<?php

return [
    /*
        * Lipa Na MPesa credentials.
    */
    'lnmo' => [
        'environment' => env('LNMO_ENVIRONMENT', 'sandbox'),
        'shortcode'   => env('LNMO_SHORTCODE', '174379'),
        'key'         => env('LNMO_KEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
        'consumer'    => [
            'key'    => env('LNMO_CONSUMER_KEY', 'uKxU78Y9q2cFruO2fKRWuofRCObzMQh8'),
            'secret' => env('LNMO_CONSUMER_SECRET', 'By9NUqT7NGhzy5Pj')
        ],
        'initiator'   => [
            'username' => env('LNMO_INITIATOR_USERNAME', 'testapi779'),
            'password' => env('LNMO_INITIATOR_PASSWORD', 'HaVh3tgp')
        ]
    ],
    /*
        * Client to Business credentials.
    */
    'c2b' => [
        'environment' => env('C2B_ENVIRONMENT', 'sandbox'),
        'shortcode'   => env('C2B_SHORTCODE', '600779'),
        'consumer'    => [
            'key'    => env('C2B_CONSUMER_KEY', 'uKxU78Y9q2cFruO2fKRWuofRCObzMQh8'),
            'secret' => env('C2B_CONSUMER_SECRET', 'By9NUqT7NGhzy5Pj')
        ],
        'initiator'   => [
            'username' => env('C2B_INITIATOR_USERNAME', 'testapi779'),
            'password' => env('C2B_INITIATOR_PASSWORD', 'HaVh3tgp')
        ]
    ],
    /*
        * Business to Client credentials.
    */
    'b2c' => [
        'environment' => env('B2C_ENVIRONMENT', 'sandbox'),
        'shortcode'   => env('B2C_SHORTCODE', '600779'),
        'consumer'    => [
            'key'    => env('B2C_CONSUMER_KEY', 'uKxU78Y9q2cFruO2fKRWuofRCObzMQh8'),
            'secret' => env('B2C_CONSUMER_SECRET', 'By9NUqT7NGhzy5Pj')
        ],
        'initiator'   => [
            'username' => env('B2C_INITIATOR_USERNAME', 'testapi779'),
            'password' => env('B2C_INITIATOR_PASSWORD', 'HaVh3tgp')
        ]
    ]
];