<?php

return [
    'bvn_model' => '\App\UserProfile',
    'bvn_column' => 'bvn',
    'debit_wallet_url' => env('DEBIT_WALLET_TRANSACTION_URL'),
    'debit_wallet_finalize_url' => env('DEBIT_WALLET_FINALIZE_URL'),
    'credit_wallet_url' => env('CREDIT_WALLET_TRANSACTION_URL'),
    'credit_wallet_finalize__url' => env('CREDIT_WALLET_FINALIZE_URL'),
    'transfer_url' => env('EXPENSE_TRANSFER_URL'),
    'bills_url' => env('EXPENSE_BILLS_URL'),
    'wallet_private_key' => env('WALLET_PRIVATE_KEY'),
    'expense_private_key' => env('EXPENSE_SERVICE_PRIVATE_KEY'),
];
