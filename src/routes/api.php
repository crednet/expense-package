<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Credpal\Expense\Http\Controllers',
    'prefix' => 'api/expense',
], function () {
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransferController@webhook');

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
});



//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
