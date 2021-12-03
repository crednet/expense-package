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
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransferController@webhook');

    Route::get('billers', 'BaxiController@getBillers');
    Route::get('biller-services', 'BaxiController@getBillerServices');
    Route::get('biller-categories', 'BaxiController@getAllBillerCategory');
    Route::get('biller-by-category/{category}', 'BaxiController@getBillerByCategory');

    Route::get('airtime-providers', 'BaxiController@getAirtimeProviders');
    Route::post('airtime-request', 'BaxiController@airtimeRequest');
    
    Route::group([
        'prefix' => 'trips'
    ], function (){
        Route::post('search', 'TripsController@search');
        Route::post('confirm-ticket', 'TripsController@confirmTicket');
        Route::post('book', 'TripsController@bookTicket');
        Route::post('cancel-ticket', 'TripsController@cancelTicket');
        Route::post('flight-rules', 'TripsController@flightRule');
        Route::post('flight-reservation', 'TripsController@myFlightReservation');
    });

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
    Route::post('electricity-request', 'BaxiController@electricityRequest');
});

/** ========== Admin Expense Route ========= **/
Route::group([
    'namespace' => 'Credpal\Expense\Http\Controllers\Admin',
    'prefix' => 'api/admin/expense',
    'middleware' => ['auth:api']
], function () {
    Route::get('trips', 'TripsController@index');
});

//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
