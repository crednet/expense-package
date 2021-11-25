<?php

namespace Credpal\Expense\Services;

use Credpal\Expense\Utilities\Enum;
use Illuminate\Support\Collection;

class TripsService extends ExpenseProcess
{
    public const PASSENGER_TYPE_CODE = [
        "ADT",
        "CHD",
        "INF"
    ];

    public const FLIGHT_LOCAL = 'local';
    public const FLIGHT_INTERNATIONAL = 'international';

    public function search()
    {
        $tripsUrl =  config('expense.trips_url') . 'search';
        
        $requestBody = $this->credentials;

        $expenseResponse = sendRequestAndThrowExceptionOnFailure($tripsUrl, $requestBody->toArray(), getPrivateKey(Enum::EXPENSE));

        return $expenseResponse;
    }
    
    public function confirmTicketPrice()
    {
        $tripsUrl =  config('expense.trips_url') . 'confirm-ticket-price';

        $requestBody = $this->credentials;

        $expenseResponse = sendRequestAndThrowExceptionOnFailure($tripsUrl, $requestBody->toArray(), getPrivateKey(Enum::EXPENSE));

        return $expenseResponse;
    }
    
    public function cancel()
    {
        $requestBody = $this->credentials;

        return sendRequestAndThrowExceptionOnFailure(config('expense.trips_url') . 'cancel-ticket', $requestBody->toArray(), getPrivateKey(Enum::EXPENSE));
    }

    public function bookTicket()
    {
        return $this->initiateTransaction(Enum::TRIPS, $this->credentials->toArray(), config('expense.trips_url') . 'book-ticket');
    }
    
    public function flightRules()
    {
        return sendRequestAndThrowExceptionOnFailure(config('expense.trips_url') . 'flight-rules', $this->credentials->toArray(), getPrivateKey(Enum::EXPENSE));
    }

    public function myReservation()
    {
        return sendRequestAndThrowExceptionOnFailure(config('expense.trips_url') . 'my-reservation', $this->credentials->toArray(), getPrivateKey(Enum::EXPENSE));
    }
}