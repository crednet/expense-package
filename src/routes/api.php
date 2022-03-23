<?php

use Illuminate\Support\Facades\Route;

Route::get('/api/expense', function() {
	return 'Expense package';
});

Route::group([
	'namespace' => 'Credpal\Expense\Http\Controllers',
	'prefix' => 'api/expense',
	'middleware' => ['auth:api']
], function () {
	Route::post('transfers', 'TransferController@transfer');
//	Route::post('transfers', 'TransferController@store');
	Route::post('webhook/transfers', 'TransferController@webhook');

	Route::group([
		'prefix' => 'bills'
	], function (){
		Route::get('transactions', 'BaxiController@getAllBillTransactions');
		Route::get('billers', 'BaxiController@getBillers');
		Route::get('biller-services', 'BaxiController@getBillerServices');
		Route::get('biller-categories', 'BaxiController@getAllBillerCategory');
		Route::get('biller-by-category/{category}', 'BaxiController@getBillerByCategory');

		Route::get('airtime-providers', 'BaxiController@getAirtimeProviders');
		Route::post('airtime-request', 'BaxiController@airtimeRequest');

		Route::get('databundle-providers', 'BaxiController@getDatabundleProviders');
		Route::get('provider-bundles/{provider}', 'BaxiController@getBundleByProvider');
		Route::post('databundle-request', 'BaxiController@dataBundleRequest');

		Route::post('verify-account-details', 'BaxiController@verifyAccountDetails');

		Route::get('cabletv-providers', 'BaxiController@getCabletvProviders');
		Route::get('multichoice-bundles-list/{provider}', 'BaxiController@getMultichoiceBundles');
		Route::post('multichoice/addons', 'BaxiController@getMultichoiceAddons');
		Route::post('multichoice-request', 'BaxiController@multichoiceRequest');

		Route::get('electricity-billers', 'BaxiController@getElectricityBillers');
		Route::post('verify-electricity-user', 'BaxiController@verifyElectricityUser');

		Route::get('{reference}', 'BaxiController@fetchBillTransaction');

		Route::group(['middleware' => [
			config('expense.blacklisted'),
			config('expense.check_airtime_daily_usage'),
			config('expense.post_no_debit'),
			config('expense.transaction_pin'),
			config('expense.trusted_device'),
		]], function () {
			Route::post('airtime-request', 'BaxiController@airtimeRequest');
			Route::post('databundle-request', 'BaxiController@dataBundleRequest');
			Route::post('multichoice-request', 'BaxiController@multichoiceRequest');
			Route::post('electricity-request', 'BaxiController@electricityRequest');
		});
	});

//	Route::group([
//		'prefix' => 'trips'
//	], function (){
//		Route::post('search', 'TripsController@search');
//		Route::post('confirm-ticket', 'TripsController@confirmTicket');
//		Route::post('book', 'TripsController@bookTicket');
//		Route::post('cancel-ticket', 'TripsController@cancelTicket');
//		Route::post('flight-rules', 'TripsController@flightRule');
//		Route::post('flight-reservation', 'TripsController@myFlightReservation');
//		Route::get('airport-list', 'TripsController@getAirportList');
//	});
});

/** ========== Admin Expense Route ========= **/
//Route::group([
//	'namespace' => 'Credpal\Expense\Http\Controllers\Admin',
//	'prefix' => 'api/admin/expense',
//	'middleware' => ['auth:api']
//], function () {
//	Route::get('trips', 'TripsController@index');
//});
