<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Credpal\Expense\Http\Controllers',
    'prefix' => 'expense',
], function () {
    Route::post('transfers', 'TransferController@store');
    Route::post('webhook/transfers', 'TransferController@webhook');

    Route::post('trips/search', 'TripsController@search');
    Route::post('trips/confirm-ticket', 'TripsController@confirmTicket');
    Route::post('trips/book', 'TripsController@bookTicket');
    Route::post('trips/cancel-ticket', 'TripsController@cancelTicket');
    Route::post('trips/flight-rules', 'TripsController@flightRule');
    Route::post('trips/flight-reservation', 'TripsController@myFlightReservation');

	Route::post('airtime-request', 'BaxiController@airtimeRequest');

});



//Route::group(['middleware' => 'auth:api', 'prefix' => 'expense-service'], function () {
//
//});
