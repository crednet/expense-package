<?php

use Illuminate\Support\Facades\Route;


    Route::get('/api/expense', function() {
    	return 'Expense package';
    });

Route::group([
    'namespace' => 'Credpal\Expense\Http\Controllers',
    'prefix' => 'api/expense',
], function () {
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransfersController@webhook');

    Route::post('airtime-request', 'BaxiController@airtimeRequest');
});



//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
