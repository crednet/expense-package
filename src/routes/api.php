<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Credpal\ExpenseService\Http\Controllers',
    'prefix' => 'expense',
], function () {
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransfersController@webhook');
});



//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
