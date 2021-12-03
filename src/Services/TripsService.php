<?php

namespace Credpal\Expense\Services;

use App\Configuration;
use Credpal\Expense\Models\TripsTravellers;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Models\Trips;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Support\Facades\DB;

class TripsService extends ExpenseProcess
{
    public const PASSENGER_TYPE_CODE = [
        "ADT",
        "CHD",
        "INF"
    ];
    
    public const TYPE_FLIGHT = "flight";

    public const FLIGHT_LOCAL = 'local';
    public const FLIGHT_INTERNATIONAL = 'international';

    public function search()
    {
        $tripsUrl =  config('expense.expense.base_uri') . 'search';
        
        $requestBody = $this->credentials;

        $expenseResponse = sendRequestAndThrowExceptionOnFailure($tripsUrl, $requestBody->toArray(), getPrivateKey(Enum::EXPENSE));

        return $expenseResponse;
    }
    
    public function confirmTicketPrice()
    {
        $tripsUrl =  config('expense.expense.base_uri') . 'confirm-ticket-price';

        $requestBody = $this->credentials;

        $expenseResponse = sendRequestAndThrowExceptionOnFailure($tripsUrl, $requestBody->toArray(), getPrivateKey(Enum::EXPENSE));

        return $expenseResponse;
    }
    
    public function cancel()
    {
        $requestBody = $this->credentials;

        return sendRequestAndThrowExceptionOnFailure(config('expense.expense.base_uri') . 'cancel-ticket', $requestBody->toArray(), getPrivateKey(Enum::EXPENSE));
    }

    public function bookTicket()
    {
        self::logTripsRequest($this->credentials->toArray());

        return $this->initiateTransaction(Enum::TRIPS, $this->credentials->toArray(), config('expense.expense.base_uri') . 'book-ticket');
    }
    
    public function flightRules()
    {
        return sendRequestAndThrowExceptionOnFailure(config('expense.expense.base_uri') . 'flight-rules', $this->credentials->toArray(), getPrivateKey(Enum::EXPENSE));
    }

    public function myReservation()
    {
        return sendRequestAndThrowExceptionOnFailure(config('expense.expense.base_uri') . 'my-reservation', $this->credentials->toArray(), getPrivateKey(Enum::EXPENSE));
    }

    /**
     * @param int $userId
     * @param int $accountId
     * @param string $userType
     * @param int $amount
     * @param string $reference
     * @param string $sessionId
     * @param string $type
     * @param string $status
     * @param string $paymentMethod
     * @param string|null $recipientNumber
     * @param null $data
     */
    public function logTripsRequest(array $data) : bool
    {
//        $markupPercentage = (float) Configuration::value('trips_mark_up_percentage', 0);
//        $markupPrice = ($markupPercentage/100) * $data["amount"];
        try {
            DB::beginTransaction();

            $trips = Trips::create(
                [
                    'user_id' => $data['user_id'],
                    'account_id' => $data['account_id'] ?? null,
                    'wallet_id' => $data['wallet_id'] ?? null,
                    'user_type' => "personal",//$data['user_type'],
                    'amount' => $data['amount'],
                    'mark_up_percentage' => Configuration::value('trips_mark_up_percentage', 0),
                    'reference' => $this->reference,
                    'session_id' => $data['session_id'],
                    'type' => $data['type'],
                    'status' => 'pending',
                    'payment_method' => $data['payment_method'],
                    'recipient_number' => $data['billing_address']['contact_mobile_no'],
                    'address' => $data['billing_address']['address_line_1'],
                    'city' => $data['billing_address']['city'],
                    'country_code' => $data['billing_address']['country_code'],
                    'contact_mobile_no' => $data['billing_address']['contact_mobile_no'],
                    'contact_email' => $data['billing_address']['contact_email'],
                    'request_data' => json_encode($data),
                ]
            );

            foreach ($data['air_travellers'] as $traveller)
            {
                TripsTravellers::create([
                    "passenger_type_code" => $traveller["passenger_type_code"],
                    "first_name" => $traveller["first_name"],
                    "last_name" => $traveller["last_name"],
                    "middle_name" => $traveller["middle_name"] ?? null,
                    "dob" => $traveller["birth_date"],
                    "title" => $traveller["name_prefix"],
                    "gender" => $traveller["gender"],
                    "address" => json_encode($traveller["address"]),
                    "documents" => json_encode($traveller["documents"]),
                ]);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ExpenseException($e);
        }

        return false;
    }
}