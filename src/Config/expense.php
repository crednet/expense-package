<?php

return [
	'profile_model' => '\App\UserProfile',
	'user_model' => '\App\User',
	'bvn_column' => 'bvn',
	'email_column' => 'email',
	'bill_transactions_model' => '\App\BillTransaction',
	'configuration_model' => 'App\Configuration',
	'transfer_model' => 'App\Models\Transfer\Transfer',
	'credit_card_transaction' => '\App\Services\V2\CreditCard\CreditCardTransactionService',
	'datatable_class' => 'App\Helpers\Datatable',
	// middleware section
	'blacklisted' => '\App\Http\Middleware\Blacklisted::class',
	'check_airtime_daily_usage' => '\App\Http\Middleware\CheckAirtimeDailyUsage::class',
	'transaction_pin' => '\App\Http\Middleware\Auth\TransactionPinMiddleware::class',
	'trusted_device' => '\App\Http\Middleware\Auth\TrustedDeviceMiddleware::class',
	'daily_cash_transaction' => '\App\Http\Middleware\Transaction\DailyCashTransactionMiddleware::class',
	'daily_transfer_count' => '\App\Http\Middleware\Transaction\DailyTransferCountMiddleware::class',

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
