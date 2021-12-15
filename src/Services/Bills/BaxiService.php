<?php

namespace Credpal\Expense\Services\Bills;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Services\DuplicateTransactionService;
use Credpal\Expense\Services\ExpenseProcess;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class BaxiService extends ExpenseProcess
{
	protected Collection $credentials;
	protected array $requestBody;
	protected $billsTransaction;

	public function __construct($credentials)
	{
		$this->billsTransaction = config('expense.bill_transactions_model');
		parent::__construct($credentials);
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function requestAirtime(): array
	{
        DuplicateTransactionService::checkDuplicateTransaction(
            Enum::AIRTIME,
            $this->credentials['phone'],
            $this->credentials->toArray()
        );
        
		$this->requestBody = [
			'phone' => $this->credentials['phone'],
			'service_type' => $this->credentials['service_type'],
			'plan' => $this->credentials['plan'],
		];
		$this->logBillsTransactions(ENUM::AIRTIME, $this->credentials['phone']);
		$response = $this->initiateTransaction(ENUM::AIRTIME, $this->requestBody, 'bills/baxi/airtime-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function requestDatabundle(): array
	{
		$this->requestBody = [
			'phone' => $this->credentials['phone'],
			'service_type' => $this->credentials['service_type'],
			'datacode' => $this->credentials['datacode'],
		];
		$this->logBillsTransactions(ENUM::DATABUNDLE, $this->credentials['phone']);
		$response = $this->initiateTransaction(ENUM::DATABUNDLE, $this->requestBody, 'bills/baxi/databundle-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function multichoiceRequest(): array
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
		$this->logBillsTransactions(ENUM::MULTICHOICE_SUBSCRIPTION, $this->credentials['smartcard_number']);
		$response = $this->initiateTransaction(ENUM::MULTICHOICE_SUBSCRIPTION, $this->requestBody, 'bills/baxi/multichoice-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function electricityRequest(): array
	{
		$this->requestBody = [
			'service_type' => $this->credentials['service_type'],
			'account_number' => $this->credentials['account_number'],
			'phone' => $this->credentials['phone'],
		];
		$this->logBillsTransactions(ENUM::ELECTRICITY_REQUEST, $this->credentials['account_number']);
		$response = $this->initiateTransaction(ENUM::ELECTRICITY_REQUEST, $this->requestBody, 'bills/baxi/electricity-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	private function logBillsTransactions($type, $recipientNumber): void
	{
		$this->billsTransaction::transactionLogger(
			$this->credentials['account_id'] ?? null,
			$this->credentials['wallet_id'] ?? null,
			$this->credentials['amount'],
			$this->reference,
			$type,
			ENUM::PENDING,
			$this->credentials['wallet_type'] === ENUM::CREDIT ? 'credpal_card' : 'credpal_cash',
			$recipientNumber
		);
	}

	private function updateBillsTransactions($response): void
	{
		$this->billsTransaction::updateTransactionLog(
			$this->reference,
			$response['status'] ? ENUM::SUCCESS : ENUM::PENDING,
			$response
		);
	}
}
