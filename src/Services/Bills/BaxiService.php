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
	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function requestAirtime(): array
	{
		DuplicateTransactionService::checkDuplicateTransaction(
			Enum::AIRTIME,
            $this->credentials['wallet_type'],
			$this->credentials['phone'],
			$this->credentials->toArray()
		);

		$this->expenseRequestBody = [
			'phone' => $this->credentials['phone'],
			'service_type' => $this->credentials['service_type'],
			'plan' => $this->credentials['plan'],
		];
		$response = $this->initiateTransaction(ENUM::AIRTIME, 'bills/baxi/airtime-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function requestDatabundle(): array
	{
		DuplicateTransactionService::checkDuplicateTransaction(
			Enum::DATABUNDLE,
            $this->credentials['wallet_type'],
			$this->credentials['phone'],
			$this->credentials->toArray()
		);

		$this->expenseRequestBody = [
			'phone' => $this->credentials['phone'],
			'service_type' => $this->credentials['service_type'],
			'datacode' => $this->credentials['datacode'],
		];
		$response = $this->initiateTransaction(ENUM::DATABUNDLE, 'bills/baxi/databundle-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function multichoiceRequest(): array
	{
		$data = $this->credentials->toArray();
		$data['amount'] = $this->credentials['amount'];

		DuplicateTransactionService::checkDuplicateTransaction(
			Enum::MULTICHOICE_SUBSCRIPTION,
            $this->credentials['wallet_type'],
			$this->credentials['smartcard_number'],
			$data
		);

		$this->expenseRequestBody = [
			'smartcard_number' => $this->credentials['smartcard_number'],
			'total_amount' => $this->credentials['total_amount'],
			'product_code' => $this->credentials['product_code'],
			'product_monthsPaidFor' => $this->credentials['product_monthsPaidFor'],
			'addon_code' => $this->credentials['addon_code'],
			'addon_monthsPaidFor' => $this->credentials['addon_monthsPaidFor'],
			'service_type' => $this->credentials['service_type'],
		];
		$response = $this->initiateTransaction(ENUM::MULTICHOICE_SUBSCRIPTION, 'bills/baxi/multichoice-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function electricityRequest(): array
	{
		DuplicateTransactionService::checkDuplicateTransaction(
			Enum::ELECTRICITY_REQUEST,
            $this->credentials['wallet_type'],
			$this->credentials['account_number'],
			$this->credentials->toArray()
		);

		$this->expenseRequestBody = [
			'service_type' => $this->credentials['service_type'],
			'account_number' => $this->credentials['account_number'],
			'phone' => $this->credentials['phone'],
		];
		$response = $this->initiateTransaction(ENUM::ELECTRICITY_REQUEST, 'bills/baxi/electricity-request');
		$this->updateBillsTransactions($response);
		return $response;
	}

	private function updateBillsTransactions($response): void
	{
		$this->billsTransaction::updateTransactionLog(
			$this->reference,
			$response['status'] ? ENUM::SUCCESS : ENUM::PENDING,
			$response['data']['transactionMessage'] ?? null,
			$response['data']
		);
	}
}
