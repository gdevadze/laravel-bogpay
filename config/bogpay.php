<?php

return [

    /*
     * Debug transactions (Log every action in database)
     */
    'debug' => env('BOGPAY_DEBUG', true),
    

    /*
     * Default currency code (ISO 4217)
     * http://en.wikipedia.org/wiki/ISO_4217
     * GEL = 981
     */
    'default_currency_code' => env('BOGPAY_DEFAULT_CURRENCY', 'GEL'),
];
