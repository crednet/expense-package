<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Credpal\Expense\Http\Controllers',
    'prefix' => 'api/expense',
], function () {
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransfersController@webhook');

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
    Route::post('electricity-request', 'BaxiController@electricityRequest');
});

//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
