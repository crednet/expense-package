<?php

namespace Credpal\Expense\Services;

use App\UserProfile;
use Credpal\Expense\Contract\ExpenseContract;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ExpenseProcess implements ExpenseContract
{
	/**
	 * @var Collection
	 */
	protected Collection $credentials;
	/**
	 * @var string
	 */
	protected string $reference;
	/**
	 * @var array
	 */
	private array $walletResponse;
	/**
	 * @var array
	 */
	private array $expenseResponse;
	/**
	 * @var string
	 */
	protected $walletType;

	protected $creditCardTransaction;

	protected string $type;

	public function __construct(Collection $credentials)
	{
		$this->credentials = $credentials;
		$this->walletType = $credentials['wallet_type'] ?? null;
		$this->reference = getReference();
		$this->credentials['reference'] = $this->reference;
		if ($this->walletType === Enum::CREDIT) {
			$creditCardTransactionModel = config('expense.credit_card_transaction');
			$this->creditCardTransaction =
				new $creditCardTransactionModel(
					$this->credentials['account_id'],
					$this->credentials['amount']
				);
		}
	}

	/**
	 * @param string $type
	 * @return $this
	 * @throws ExpenseException
	 */
	private function withdrawAmount(string $type): ExpenseProcess
	{
		/*
		$walletUrl = ($this->walletType === Enum::DEBIT) ?
			config('expense.cash.base_url'). 'wallets' :
			config('expense.credit_wallet_url');
		*/
		$requestBody = [
			'category' => $type,
			'amount' => $this->credentials['amount'],
			'reference' => $this->reference,
			'description' => $this->credentials['description'] ?? $type
		];

		if ($this->walletType === Enum::DEBIT) {
			$this->withdrawFromCash($requestBody);
		} elseif($this->walletType === Enum::CREDIT) {
			$this->type = $type;
			$this->withdrawFromCredit($requestBody);
		}

		return $this;
	}

	/**
	 * @param $requestBody
	 * @return void
	 * @throws ExpenseException
	 */
	private function withdrawFromCash($requestBody): void
	{
		$walletUrl = config('expense.cash.base_url') . 'wallets';
		$requestBody['wallet_id'] = $this->credentials['wallet_id'];
		$requestBody['cbs_account_number'] = $this->credentials['wallet_account_number'] ?? null;

		$this->walletResponse = sendRequestAndThrowExceptionOnFailure(
			"$walletUrl/{$this->credentials['wallet_id']}/withdraw",
			$requestBody,
			getPrivateKey(Enum::WALLET)
		);
	}

	private function withdrawFromCredit($requestBody): void
	{
		$this->creditCardTransaction->checkLocalAccount();
		$this->creditCardTransaction->makeWithdrawal();
	}

	/**
	 * @throws ExpenseException
	 */
	private function processTransaction(string $type, array $requestBody, string $urlPath): ExpenseProcess
	{
		/*
		$transactionUrl = ($type === ENUM::TRANSFER) ?
			config('expense.transfer_url') :
			config('expense.bills_url') . $url;
		*/
		$transactionUrl = config('expense.expense.base_url') . '/' . $urlPath;

		$bvnModel = config('expense.bvn_model');
		$bvnColumn = config('expense.bvn_column');
		$bvnInstance = new $bvnModel();
		$requestBody['bvn'] = $bvnInstance->query()
			->whereUserId($this->credentials['user_id'])
			->firstOrFail()
			->{$bvnColumn};

		$requestBody['amount'] = $this->credentials['amount'] ?? $this->walletResponse['data']['transaction']['amount'];
		$requestBody['description'] = $this->credentials['description'] ?? $this->walletResponse['data']['transaction']['description'] ?? $type;
		$requestBody['reference'] = $this->reference ?? $this->walletResponse['data']['transaction']['reference'];

		$this->expenseResponse = sendRequestTo($transactionUrl, $requestBody, getPrivateKey(Enum::EXPENSE));

		return $this;
	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	private function processExpenseResponse(): array
	{
		$this->reverseWalletAndNotifyUserIfTransactionFailedOnInitiation($this->expenseResponse);
		return $this->notifyUserOnSuccessfulTransactionInitiation($this->expenseResponse);
	}

	/**
	 * @param array $expenseResponse
	 * @throws ExpenseException
	 */
	private function reverseWalletAndNotifyUserIfTransactionFailedOnInitiation(array $expenseResponse): void
	{
		$status = $expenseResponse['status'];

		$reference = $this->reference ?? $expenseResponse['data']['reference'];

		if (!$status) {
			// update to reverse wallet if the transfer failed
			/*
			$walletUpdateUrl = ($this->walletType === Enum::DEBIT) ?
				config('expense.cash.base_url') . 'wallets/' . $this->credentials['wallet_id'] :
				config('expense.credit_wallet_finalize_url');
			*/

			if ($this->walletType === Enum::DEBIT) {
				$this->reverseCash($status, $reference);
			} elseif($this->walletType === Enum::CREDIT) {
				$this->reverseCredit($status, $reference);
			}

		}
	}

	/**
	 * @param $status
	 * @param $reference
	 * @throws ExpenseException
	 */
	private function reverseCash($status, $reference): void
	{
		$walletUpdateUrl = config('expense.cash.base_url') . 'wallets/' . $this->credentials['wallet_id'] . '/transactions/' . $reference;

		$requestBody = [
			'reference' => $reference,
			'status' => 'refunded',//Enum::FAILED
		];
		// There is an edge case here. Assuming the wallet service was not available at this point. It means
		// it wont be updated/reversed for a while. So that is why jobs and queues should be used,
		// So that whenever it comes online it can pick jobs from the queue
		sendRequestTo($walletUpdateUrl, $requestBody, getPrivateKey(Enum::WALLET), 'put');
	}

	private function reverseCredit($status, $reference): void
	{
		$this->creditCardTransaction->updateAccount(
			$this->credentials['account_id'],
			$this->credentials['amount'],
			$status
		);
	}


	/**
	 * @param array $expenseResponse
	 * @return array
	 * @throws ExpenseException
	 */
	private function notifyUserOnSuccessfulTransactionInitiation(array $expenseResponse): array
	{
		$status = $expenseResponse['status'];
		if (!$status) {
			// Notify the user here
			throw new ExpenseException(
				trans('expense::exception.unsuccessful_transaction'),
				Response::HTTP_PRECONDITION_FAILED
			);
		}
//		if ($this->walletType === Enum::DEBIT) {

		if($this->walletType === Enum::CREDIT) {
			$this->creditCardTransaction->logTransactions(
				$this->credentials['account_id'],
				$this->credentials['amount'],
				$status,
				$this->type,
				$this->credentials['description'] ?? $this->credentials['service_type'] ?? $this->type
			);
		}
		return $expenseResponse;
	}


	/**
	 * @param string $type
	 * @param array $requestBody
	 * @param string|null $url
	 * @return array
	 * @throws ExpenseException
	 */
	public function initiateTransaction(string $type, array $requestBody, string $url = null): array
	{
		return $this->withdrawAmount($type)->processTransaction($type, $requestBody, $url)->processExpenseResponse();
	}

	/**
	 * @param string $reference
	 * @param string $status
	 * @return array
	 * @throws ExpenseException
	 */
	public static function finalizeTransaction(string $reference, string $status): array
	{
		$debitWalletUrl = config('expense.debit_wallet_finalize_url');
		$creditWalletUrl = config('expense.credit_wallet_finalize_url');
		$requestBody = [
			'reference' => $reference,
			'status' => $status
		];
		//will send request to both since at this point there is no way to figure out which wallet type
		$debitWalletResponse = sendRequestTo($debitWalletUrl, $requestBody, getPrivateKey(Enum::WALLET));
		$creditWalletResponse = sendRequestTo($creditWalletUrl, $requestBody, getPrivateKey(Enum::WALLET));
		if (!$debitWalletResponse['status'] || !$creditWalletResponse['status']) {
			throw new ExpenseException(
				trans('expense::exception.unsuccessful_webhook'),
				Response::HTTP_PRECONDITION_FAILED
			);
		}
		return $requestBody;
	}
}
