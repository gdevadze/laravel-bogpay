<?php

return [

    /*
     * Debug transactions (Log every action in database)
     */
    'debug' => env('BOGPAY_DEBUG', true),

    /**
     * Client ID provided by Bank of Georgia
     */
    'client_id' => env('BOGPAY_CLIENT_ID'),

    /**
     * Default language for Bank of Georgia payment
     */
    'language' => env('BOGPAY_LANGUAGE', 'ka'),

    /**
     * Client Secret provided by Bank of Georgia
     */
    'client_secret' => env('BOGPAY_CLIENT_SECRET'),

    /**
     * Callback URL
     */
    'callback_url' => env('BOGPAY_CALLBACK_URL'),

    /*
     * Default currency code
     */
    'default_currency_code' => env('BOGPAY_DEFAULT_CURRENCY', 'GEL'),
];
