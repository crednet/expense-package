<?php

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Traits\ExpenseError;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
define('WITH_EXCEPTION', true);
define('WITHOUT_EXCEPTION', true);

if (!function_exists('getReference')) {
    /**
     * @param int $lengthOfString
     * @return string
     */
    function getReference(int $lengthOfString = 15): string
    {
        $strResult = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        return strtolower(substr(str_shuffle($strResult), 0, $lengthOfString));
    }
}


if (!function_exists('processResponse')) {
    /**
     * @param mixed $response
     * @return array
     */
    function processResponse($response): array
    {
        $data = $response->json();
        $message = $data['message'];
        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => $message,
                'errors' => $data['errors'] ?? [],
            ];
        }
        $data = $data['data'];
        return [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
    }
}


if (!function_exists('processResponseWithException')) {
    /**
     * @param mixed $response
     * @param null|string $message
     * @return array
     * @throws ExpenseException
     */
    function processResponseWithException($response, string $message = null): array
    {
        $data = processResponse($response);
        $status = $data['status'];
        $msg = $message ?? $data['message'];
        if ($response->status() === Response::HTTP_UNPROCESSABLE_ENTITY) {
            ExpenseError::setErrors($response->json()['errors']);
        }

        if ((isset($data['success']) && !$data['success']) || !$status) {
            // This will terminate the whole process and notify this user
            ExpenseError::abortIfUnsuccessfulResponse($data['message'], $response->status());
        }
        return $data;
    }
}


if (!function_exists('sendRequestAndThrowExceptionOnFailure')) {
	/**
	 * @param string $url
	 * @param array|null $requestBody
	 * @param string $privateKey
	 * @param string $method
	 * @param null|string $customMessage
	 * @return array
	 * @throws ExpenseException
	 */
    function sendRequestAndThrowExceptionOnFailure(
        string $url,
        ?array $requestBody,
		string $privateKey,
		string $method = 'post',
        string $customMessage = null
    ): array {
        $response = Http::acceptJson()->withToken($privateKey)->$method($url, $requestBody);
        return processResponseWithException($response, $customMessage);
    }
}


if (!function_exists('sendRequestTo')) {
	/**
	 * @param string $url
	 * @param array|null $requestBody
	 * @param string $privateKey
	 * @param string $method
	 * @return array
	 */
    function sendRequestTo(string $url, ?array $requestBody, string $privateKey, string $method = 'post'): array
    {
        $response = Http::acceptJson()->withToken($privateKey)->$method($url, $requestBody);
        return processResponse($response);
    }
}

if (!function_exists('getPrivateKey')) {
    /**
     * @param string $serviceType
     * @return string
     * @throws ExpenseException
     */
    function getPrivateKey(string $serviceType): string
    {
		$env = config('app.env');
		switch ($serviceType) {
			case Enum::WALLET:
				switch ($env) {
					case Enum::PRODUCTION:
						$key = config('expense.cash.private_key.live');
						break;
					default:
						$key = config('expense.cash.private_key.test');
				}
				break;
            case Enum::EXPENSE:
                $key = config('expense.expense.private_key');
                break;
            default:
                $key = null;
                ExpenseError::abortIfNoServiceIsSpecified();
                break;
        }
        return $key;
    }
}