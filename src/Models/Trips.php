<?php

namespace Credpal\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trips extends Model
{
    use SoftDeletes;


    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $guarded = [
        //
    ];

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
//        string $serviceProvider,
        string $reference,
        string $sessionId,
        string $type,
        string $status,
//        string $url,
        string $paymentMethod,
        string $recipientNumber = null,
//        string $description = null,
//        string $transactionReference = null,
        array $requestData = null
    ) {

        self::create(
            [
                'user_id' => $userId,
                'account_id' => $accountId,
                'user_type' => $userType,
                'amount' => $amount,

//                'service_provider' => $serviceProvider,
                'reference' => $reference,
                'session_id' => $sessionId,
                'type' => $type,
                'status' => $status,
//                'url' => $url,
                'payment_method' => $paymentMethod,
                'recipient_number' => $recipientNumber,
//                'action' => ($status === ENUM::PENDING) ? ENUM::RETRY : ENUM::SKIP,
//                'description' => $description,
//                'transaction_reference' => $transactionReference,
                'request_data' => json_encode($requestData),
//                'data' => $data,
            ]
        );
    }
}
