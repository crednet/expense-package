<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Http\Requests\Trips\BookTicketRequest;
use Credpal\Expense\Http\Requests\Trips\CancelRequest;
use Credpal\Expense\Http\Requests\Trips\ConfirmTicketPriceRequest;
use Credpal\Expense\Http\Requests\Trips\FlightReservationRequest;
use Credpal\Expense\Http\Requests\Trips\FlightRulesRequest;
use Credpal\Expense\Http\Requests\Trips\SearchRequest;
use Credpal\Expense\Services\TripsService;
use Credpal\Expense\Utilities\Enum;

class TripsController extends Controller
{
    public function search(SearchRequest $request)
    {
        $searchResult = sendRequestAndThrowExceptionOnFailure(
            config('expense.expense.base_url') . '/trips/search',
            $request->validated(),
            getPrivateKey(Enum::EXPENSE)
        );
        $configurationModel = config('expense.configuration_model');

        $searchResult['data']['mark_up_percentage'] = $configurationModel::value('trips_mark_up_percentage');
        
        return $this->success($searchResult['data']);
    }
    
    public function confirmTicket(ConfirmTicketPriceRequest $request)
    {
        $requestBody = $request->validated();

        $user = auth()->user();

        $requestBody['billing_address'] = [
            'contact_name' => $user->name . ' ' . $user->last_name,
            'address_line_1' => $user->profile->address,
            'city' => $user->profile->lga,
            'country_code' => 'NG',
            'contact_mobile_no' => $user->phone_no,
            'contact_email' => $user->email,
        ];

        $confirmTicketResponse = sendRequestAndThrowExceptionOnFailure(
            config('expense.expense.base_url') . '/trips/confirm-ticket-price',
            $requestBody,
            getPrivateKey(Enum::EXPENSE)
        );

        $confirmTicketResponse = TripsService::resultData($request->flight_type, $confirmTicketResponse);
        $configurationModel = config('expense.configuration_model');

        $confirmTicketResponse['data']['mark_up_percentage'] = $configurationModel::value('trips_mark_up_percentage');

        return $this->success($confirmTicketResponse['data']);
    }

    public function cancelTicket(CancelRequest $request)
    {
        $response = sendRequestAndThrowExceptionOnFailure(
            config('expense.expense.base_url') . '/trips/cancel-ticket',
            $request->validated(),
            getPrivateKey(Enum::EXPENSE)
        );

        return $this->success($response['data']);
    }

    public function bookTicket(BookTicketRequest $request)
    {
        $requestBody = $request->validated();

        $user = auth()->user();

        $requestBody['billing_address'] = [
            'contact_name' => $user->name . ' ' . $user->last_name,
            'address_line_1' => $user->profile->address,
            'city' => $user->profile->lga,
            'country_code' => 'NG',
            'contact_mobile_no' => $user->phone_no,
            'contact_email' => $user->email,
        ];

        $tripsService = new TripsService(collect($requestBody));
        $tripsService = $tripsService->bookTicket();

        return $this->success($tripsService['data']);
    }
    public function flightRule(FlightRulesRequest $request)
    {
        $flightRules = sendRequestAndThrowExceptionOnFailure(
            config('expense.expense.base_url') . '/trips/flight-rules',
            $request->validated(),
            getPrivateKey(Enum::EXPENSE)
        );

        return $this->success($flightRules['data']);
    }
    
    public function myFlightReservation(FlightReservationRequest $request)
    {
        $myReservations = sendRequestAndThrowExceptionOnFailure(
            config('expense.expense.base_url') . '/trips/my-reservation',
            $request->validated(),
            getPrivateKey(Enum::EXPENSE)
        );

        return $this->success($myReservations['data']);
    }
}
