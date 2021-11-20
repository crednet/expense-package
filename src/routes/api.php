<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Credpal\Expense\Http\Controllers',
    'prefix' => 'expense',
], function () {
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransferController@webhook');

	Route::post('airtime-request', 'BaxiController@airtimeRequest');

});



//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
