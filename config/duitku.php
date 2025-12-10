<?php

return [
    'merchant_code' => env('DUITKU_MERCHANT_CODE'),
    'merchant_key'  => env('DUITKU_MERCHANT_KEY'),
    'is_sandbox'    => env('DUITKU_IS_SANDBOX', true),

    // boleh pakai ini atau generate dari route()
    'callback_url'  => env('DUITKU_CALLBACK_URL'),
    'return_url'    => env('DUITKU_RETURN_URL'),
];
