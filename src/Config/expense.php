<?php

return [
	'bvn_model' => '\App\UserProfile',
	'personal_card_account_model' => '\App\PersonalCardAccount',
	'statement_service' => '\App\Services\V2\CreditCard\StatementService',
	'reconciliation_service' => '\App\Services\ReconciliationService',
	'bvn_column' => 'bvn',
	'bills_url' => env('EXPENSE_BILLS_URL'),

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
