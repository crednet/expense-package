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
			'account_number' => $this->credentials['account_number'],
			'recipient_number' => $this->credentials['recipient_number'],
			'service_type' => $this->credentials['service_type'],
			'plan' => $this->credentials['plan'],
		];
		return $this->initiateTransaction(ENUM::AIRTIME, $this->requestBody, 'baxi/airtime-request');
	}
}
