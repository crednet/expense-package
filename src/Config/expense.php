<?php

return [
	'bvn_model' => '\App\UserProfile',
	'bill_transactions_model' => '\App\BillTransaction',
    'configuration_model' => 'App\Configuration',
	'credit_card_transaction' => '\App\Services\V2\CreditCard\CreditCardTransactionService',
	'bvn_column' => 'bvn',

	'cash' => [
		'base_url' => env('CPCASH_BASEURL'),
		'private_key' => [
			'test' => env('CPCASH_TEST_SECRET_KEY'),
			'live' => env('CPCASH_LIVE_SECRET_KEY')
		],
		'public_key' => env('CASH_PUBLIC_KEY'),
	],
	'expense' => [
		'base_url' => env('EXPENSE_BASE_URL'),
		'private_key' => env('EXPENSE_PRIVATE_KEY'),
		'public_key' => env('EXPENSE_PUBLIC_KEY'),
	]
];
