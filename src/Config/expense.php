<?php

return [
	'bvn_model' => '\App\UserProfile',
	'bill_transactions_model' => '\App\BillTransaction',
	'credit_card_transaction' => '\App\Services\V2\CreditCard\CreditCardTransactionService',
	'bvn_column' => 'bvn',

	'cash' => [
		'base_url' => env('CASH_BASE_URL'),
		'private_key' => env('CASH_PRIVATE_KEY'),
		'public_key' => env('CASH_PUBLIC_KEY'),
	],
	'expense' => [
		'base_url' => env('EXPENSE_BASE_URL'),
		'private_key' => env('EXPENSE_PRIVATE_KEY'),
		'public_key' => env('EXPENSE_PUBLIC_KEY'),
	]
];
