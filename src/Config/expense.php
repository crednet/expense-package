<?php

return [
    'bvn_model' => '\App\UserProfile',
    'bvn_column' => 'bvn',
   'bills_url' => env('EXPENSE_BILLS_URL'),

    'cash' => [
        'base_uri' => env('CASH_BASE_URL'),
        'private_key' => env('CASH_PRIVATE_KEY'),
        'public_key' => env('CASH_PUBLIC_KEY'),
    ],
    'expense' => [
        'base_uri' => env('EXPENSE_BASE_URL'),
        'private_key' => env('EXPENSE_PRIVATE_KEY'),
        'public_key' => env('EXPENSE_PUBLIC_KEY'),
    ]
];
