<?php

namespace Credpal\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
	use SoftDeletes;

	public const PASSENGER_TYPE_CODE = [
		"ADT",
		"CHD",
		"INF"
	];

	public const FLIGHT_LOCAL = 'local';

	public const FLIGHT_INTERNATIONAL = 'international';

	public const VGG_TRIPS = 'vgg_trips';

	public const TYPE_FLIGHT = 'flight';

	protected $casts = [
		'request_data' => 'json'
	];

	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id');
	}

	public function tripTravellers()
	{
		return $this->hasMany(TripTraveller::class, 'trip_id');
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
	 * @param array|null $requestData
	 */
	public static function transactionLogger(
		int $userId,
		int $accountId,
		string $userType,
		int $amount,
		string $reference,
		string $sessionId,
		string $type,
		string $status,
		string $paymentMethod,
		string $recipientNumber = null,
		array $requestData = null
	) {

		self::create(
			[
				'user_id' => $userId,
				'account_id' => $accountId,
				'user_type' => $userType,
				'amount' => $amount,
				'reference' => $reference,
				'session_id' => $sessionId,
				'type' => $type,
				'status' => $status,
				'payment_method' => $paymentMethod,
				'recipient_number' => $recipientNumber,
				'request_data' => json_encode($requestData)
			]
		);
	}
}
