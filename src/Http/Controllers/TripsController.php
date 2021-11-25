<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Facades\CPExpense;
use Credpal\Expense\Http\Requests\Trips\BookTicketRequest;
use Credpal\Expense\Http\Requests\Trips\CancelRequest;
use Credpal\Expense\Http\Requests\Trips\ConfirmTicketPriceRequest;
use Credpal\Expense\Http\Requests\Trips\FlightReservationRequest;
use Credpal\Expense\Http\Requests\Trips\FlightRulesRequest;
use Credpal\Expense\Http\Requests\Trips\SearchRequest;
use Credpal\Expense\Services\TripsService;
use Credpal\Expense\Traits\TResponse;

class TripsController extends Controller
{
    public function search(SearchRequest $request)
    {
        $tripsService = new TripsService(collect($request->validated()));
        $tripsService = $tripsService->search();
        
        return $this->success($tripsService['data']);
    }
    
    public function confirmTicket(ConfirmTicketPriceRequest $request)
    {
        $tripsService = new TripsService(collect($request->validated()));
        $tripsService = $tripsService->confirmTicketPrice();

        return $this->success($tripsService['data']);
    }

    public function cancelTicket(CancelRequest $request)
    {
        $tripsService = new TripsService(collect($request->validated()));
        $tripsService = $tripsService->cancel();

        return $this->success($tripsService['data']);
    }

    public function bookTicket(BookTicketRequest $request)
    {
        $tripsService = new TripsService(collect($request->validated()));
        $tripsService = $tripsService->bookTicket();

        return $this->success($tripsService['data']);
    }
    public function flightRule(FlightRulesRequest $request)
    {
        $tripsService = new TripsService(collect($request->validated()));
        $tripsService = $tripsService->flightRules();
         return $this->success($tripsService['data']);
    }
    
    public function myFlightReservation(FlightReservationRequest $request)
    {
        $tripsService = new TripsService(collect($request->validated()));
        $tripsService = $tripsService->myReservation();
        return $this->success($tripsService['data']);
    }
}
