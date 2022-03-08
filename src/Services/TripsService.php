<?php

namespace Credpal\Expense\Services;

use App\Configuration;
use Credpal\Expense\Models\Trip;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Models\TripTraveller;
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

	/**
	 * @throws ExpenseException
	 */
	public function bookTicket()
	{
		$this->credentials['description'] = "Trips - " . $this->credentials['flight_type'];

		$this->expenseRequestBody = $this->credentials->toArray();

		$this->logTripsRequest($this->expenseRequestBody);

		$bookTicket = $this->initiateTransaction(Enum::TRIPS, 'trips/book-ticket');

		if ($bookTicket['status']){
			$this->updateTripsRequestLog($bookTicket['data'], Enum::SUCCESS);
		}

		return $bookTicket;
	}

	/**
	 * @param array $data
	 * @return Trip
	 */
	public function logTripsRequest(array $data) : Trip
	{
		$configurationModel = config('expense.configuration_model');

		$trips = Trip::create(
			[
				'user_id' => $data['user_id'],
				'account_id' => $data['account_id'] ?? null,
				'wallet_id' => $data['wallet_id'] ?? null,
				'wallet_type' => $data["wallet_type"],
				'user_type' => "personal",//$data['user_type'],
				'amount' => $data['amount'],
				'mark_up_percentage' => $configurationModel::value('trips_mark_up_percentage', 0),
				'transaction_reference' => $this->reference,
				'session_id' => $data['session_id'],
				'type' => $data['type'] . '-' . $data['flight_type'],
				'status' => Enum::PENDING,
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
			$dob = $this->refactorBirthDate($traveller["birth_date"]);
			TripTraveller::create([
				"trip_id" => $trips->id,
				"passenger_type_code" => $traveller["passenger_type_code"],
				"first_name" => ucfirst(strtolower($traveller["first_name"])),
				"last_name" => ucfirst(strtolower($traveller["last_name"])),
				"middle_name" => $traveller["middle_name"] ?? null,
				"dob" => $dob,
				"title" => $traveller["name_prefix"],
				"gender" => $traveller["gender"],
				"address" => json_encode($traveller["address"]),
				"documents" => json_encode($traveller["documents"] ?? []),
			]);
		}

		return $trips;
	}

	public function updateTripsRequestLog($data, $status)
	{
		$trips = Trip::where('transaction_reference', $this->reference)->firstOrFail();

		if ($this->credentials["flight_type"] === self::FLIGHT_LOCAL) {
			$data = $data[0];
		}

		$flightDetails = $data['flight_sets'][0]['flight_entries'][0];
		$airTravellers = $data["air_travellers"];

		$trips->update([
			"reference_number" => $data["reference_number"],
			"booking_reference_id" => $data["booking_reference_id"],
			"booking_reference_type" => $data["booking_reference_type"],
			"ticket_time_limit" => $data["ticket_time_limit"],
			'response_data' => json_encode($data),
			'status' => $status,
			'departure_airport_code' => $flightDetails['departure_airport_code'],
			'departure_airport_name' => $flightDetails['departure_airport_name'],
			'arrival_airport_code' => $flightDetails['arrival_airport_code'],
			'arrival_airport_name' => $flightDetails['arrival_airport_name'],
			'departure_date' => $flightDetails['departure_date'],
			'arrival_date' => $flightDetails['arrival_date'],
		]);

		foreach ($airTravellers as $air_traveller) {
			$traveller = TripTraveller::where('trip_id', $trips->id)
				->where('first_name', ucfirst(strtolower($air_traveller["first_name"])))
				->where('last_name', ucfirst(strtolower($air_traveller["last_name"])))
				->where('dob', substr($air_traveller["birth_date"], 0, 10))
				->first();

			$traveller->update([
				'e_ticket_number' => $air_traveller["e_ticket_number"],
				'traveller_reference_id' => $air_traveller["traveller_reference_id"]
			]);
		}
	}

	/**
	 * @param $dob
	 * @return string
	 */
	protected function refactorBirthDate($dob): string
	{
		$dobArray = [];

		if (strpos($dob, '/')) {
			$dobArray = explode('/', $dob);
		}
		if (strpos($dob, '-')) {
			$dobArray = explode('-', $dob);
		}
		$lengthOfFirstItem = strlen($dobArray[0]);
		$finalDobArray = ($lengthOfFirstItem > 3) ? $dobArray : array_reverse($dobArray);

		return implode('-', $finalDobArray);
	}

	public static function resultData($flightyType, $data)
	{
		if ($flightyType === self::FLIGHT_LOCAL) {
			$data['data'] = $data['data'][0];
			return $data;
		}

		return $data;
	}
}
