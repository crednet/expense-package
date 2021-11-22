<?php

namespace Credpal\Expense\Services\Bills;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Services\ExpenseProcess;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class BaxiService extends ExpenseProcess
{
    protected array $credentials;
    protected array $requestBody;

    public function __construct($credentials)
    {
        $this->credentials = $credentials;
        parent::__construct($credentials);
    }

    /**
     * @return array
     * @throws ExpenseException
     */
    public function requestAirtime(): array
    {
        $this->requestBody = [
            'recipient_number' => $this->credentials['recipient_number'],
            'service_type' => $this->credentials['service_type'],
            'plan' => $this->credentials['plan'],
        ];
        return $this->initiateTransaction(ENUM::AIRTIME, $this->requestBody, 'baxi/airtime-request');
    }

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function requestDatabundle(): array
	{
		$this->requestBody = [
			'recipient_number' => $this->credentials['recipient_number'],
			'service_type' => $this->credentials['service_type'],
			'datacode' => $this->credentials['datacode'],
		];
		return $this->initiateTransaction(ENUM::DATABUNDLE, $this->requestBody, 'baxi/databundle-request');
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function verifyAccountDetails(): array
	{
		$this->requestBody = [
			'service_type' => $this->credentials['service_type'],
			'account_number' => $this->credentials['account_number'],
		];
		return $this->initiateTransaction(ENUM::VERIFY_ACCOUNT, $this->requestBody, 'baxi/verify-account-details');
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function getMultichoiceAddons(): array
	{
		$this->requestBody = [
			'service_type' => $this->credentials['service_type'],
			'product_code' => $this->credentials['product_code'],
		];
		return $this->initiateTransaction(ENUM::MULTICHOICE_ADDON, $this->requestBody, 'baxi/multichoice/addonss');
	}

	public function multichoiceRequest()
	{
		$this->requestBody = [
			'smartcard_number' => $this->credentials['smartcard_number'],
			'total_amount' => $this->credentials['total_amount'],
			'product_code' => $this->credentials['product_code'],
			'product_monthsPaidFor' => $this->credentials['product_monthsPaidFor'],
			'addon_code' => $this->credentials['addon_code'],
			'addon_monthsPaidFor' => $this->credentials['addon_monthsPaidFor'],
			'service_type' => $this->credentials['service_type'],
		];
		return $this->initiateTransaction(ENUM::MULTICHOICE_SUBSCRIPTION, $this->requestBody, 'baxi/multichoice/addonss');
	}
}
