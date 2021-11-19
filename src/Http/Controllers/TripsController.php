<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Facades\CPExpense;
use Credpal\Expense\Http\Requests\Trips\ConfirmTicketPriceRequest;
use Credpal\Expense\Http\Requests\Trips\FlightReservationRequest;
use Credpal\Expense\Http\Requests\Trips\FlightRulesRequest;
use Credpal\Expense\Http\Requests\Trips\SearchRequest;
use Credpal\Expense\Traits\TResponse;

class TripsController extends Controller
{
    use TResponse;
    
    public function search(SearchRequest $request)
    {
        $searchResponse = CPExpense::searchTicket($request->validated());
        return $this->success($searchResponse['data']);
    }
    
    public function confirmTicket(ConfirmTicketPriceRequest $request)
    {
        $confirmTicketResponse = CPExpense::confirmTicket($request->validated());
        
        return $this->success($confirmTicketResponse['data']);
    }

    public function bookTicket(ConfirmTicketPriceRequest $request)
    {
        $confirmTicketResponse = CPExpense::bookTicket($request->validated());

        return $this->success($confirmTicketResponse['data']);
    }
    
    public function cancelTicket()
    {
        $cancelResponse = CPExpense::cancel($request->validated());

        return $this->success($cancelResponse['data']);
    }
    
    public function flightRule(FlightRulesRequest $request)
    {
        $confirmTicketResponse = CPExpense::flightRules($request->validated());

        return $this->success($confirmTicketResponse['data']);
    }

    public function myFlightReservation(FlightReservationRequest $request)
    {
        $reservations = CPExpense::flightReservations($request->validated());

        return $this->success($reservations['data']);
    }
}
