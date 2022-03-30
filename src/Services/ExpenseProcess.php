<?php

namespace Credpal\Expense\Services;

use Credpal\Expense\Contract\ExpenseContract;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Traits\BIllTransactionTrait;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ExpenseProcess implements ExpenseContract
{
	use BIllTransactionTrait;
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

	protected array $expenseRequestBody;

	protected $creditCardTransaction;

	protected string $type;

	public function __construct(Collection $credentials)
	{
		$this->credentials = $credentials;
		$this->walletType = $credentials['wallet_type'] ?? null;
		$this->reference = getReference();
		$this->credentials['reference'] = $this->reference;
		$creditCardTransactionModel = config('expense.credit_card_transaction');
		$this->creditCardTransaction =
			new $creditCardTransactionModel(
				$this->credentials['amount'] ?? null,
				$this->credentials['account_id'] ?? null,
			);
	}

	/**
	 * @param string $type
	 * @return $this
	 * @throws ExpenseException
	 */
	private function withdrawAmount(string $type): ExpenseProcess
	{
		$requestBody = [
			'category' => $type,
			'amount' => $this->credentials['amount'],
			'reference' => $this->reference,
			'description' => $this->credentials['description'] ?? $type
		];

		$this->type = $type;

		if ($this->walletType === Enum::DEBIT) {
			$this->withdrawFromCash($requestBody);
		} elseif($this->walletType === Enum::CREDIT) {
			$this->withdrawFromCredit($requestBody);
		}

		($this->type !== ENUM::TRANSFER && $this->type !== ENUM::TRIPS)
			? $this->initialTransactionLogForBills($this->type) : true;

		return $this;
	}

	/**
	 * @throws ExpenseException
	 */
	private function withdrawFromCash(array $requestBody): void
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
	private function processTransaction(string $type, string $urlPath): ExpenseProcess
	{
		$transactionUrl = config('expense.expense.base_url') . '/' . $urlPath;

		$this->accessUserBvnAndEmail();

		$this->expenseRequestBody['amount'] = $this->credentials['amount'] ?? $this->walletResponse['data']['transaction']['amount'];
		$this->expenseRequestBody['description'] = $this->credentials['description'] ?? $this->walletResponse['data']['transaction']['description'] ?? $type;
		$this->expenseRequestBody['reference'] =  $this->walletResponse['data']['transaction']['reference'] ?? $this->reference;

		$this->expenseResponse = sendRequestTo($transactionUrl, $this->expenseRequestBody, getPrivateKey(Enum::EXPENSE));

		return $this;
	}

	private function accessUserBvnAndEmail(): void
	{
		$profileModel = config('expense.profile_model');
		$bvnColumn = config('expense.bvn_column');
		$userModel = config('expense.user_model');
		$emailColumn = config('expense.email_column');
		$profileInstance = new $profileModel();
		$userInstance = new $userModel();
		$this->expenseRequestBody['bvn'] = $profileInstance->query()
				->whereUserId($this->credentials['user_id'])
				->first()->{$bvnColumn} ?? null;

		$this->expenseRequestBody['email'] = $userInstance->query()
				->whereId($this->credentials['user_id'])
				->first()->{$emailColumn} ?? null;

	}

	/**
	 * @return array
	 * @throws ExpenseException
	 */
	public function processExpenseResponse(): array
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

		\Log::info('request came from payment');

		if (!$status && $this->type === ENUM::ELECTRICITY_REQUEST) {
			throw new ExpenseException(
				'Kindly hold on while your electricity request is being processed',
				Response::HTTP_PRECONDITION_FAILED
			);
		}

		if (!$status) {
			// update to reverse wallet if the transaction failed
			$transactionReference = $this->expenseRequestBody['reference'] ?? $this->reference;
			if ($this->walletType === Enum::DEBIT) {
				$this->reverseCash($status, $transactionReference, $this->credentials['wallet_id']);
			} elseif($this->walletType === Enum::CREDIT) {
				$this->reverseCredit($status, $transactionReference, $this->credentials['account_id'], $this->credentials['amount']);
			}
		}
	}

	/**
	 * @throws ExpenseException
	 */
	public function reverseCash(bool $status, string $reference, string $walletId): void
	{
		$walletUpdateUrl = config('expense.cash.base_url') . 'wallets/' . $walletId . '/transactions/' . $reference;

		$requestBody = [
			'reference' => $reference,
			'status' => 'refunded',//Enum::FAILED
		];
		// There is an edge case here. Assuming the wallet service was not available at this point. It means
		// it wont be updated/reversed for a while. So that is why jobs and queues should be used,
		// So that whenever it comes online it can pick jobs from the queue
		sendRequestTo($walletUpdateUrl, $requestBody, getPrivateKey(Enum::WALLET), 'put');
	}

	public function reverseCredit($status, $reference, $accountId, $amount): void
	{
		$this->creditCardTransaction->updateAccount(
			$accountId,
			$amount,
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

		if ($this->walletType === Enum::DEBIT) {
			$this->creditCardTransaction->logTransactionsForCash(
				'credpal_cash',
				$this->credentials['amount'],
				$this->expenseRequestBody['reference'] ?? $this->reference,
				$this->credentials['description'] ?? $this->credentials['service_type'] ?? $this->type,
				$this->type,
				$this->credentials['wallet_id'],
				$this->expenseResponse
			);
		} elseif($this->walletType === Enum::CREDIT) {
			$this->creditCardTransaction->logTransactions(
				$this->credentials['account_id'],
				'credpal_card',
				$this->credentials['amount'],
				$this->expenseRequestBody['reference'] ?? $this->reference,
				$status,
				$this->type,
				$this->credentials['description'] ?? $this->credentials['service_type'] ?? $this->type,
				$this->expenseResponse
			);
		}

		return $expenseResponse;
	}

	/**
	 * @param string $type
	 * @param string|null $url
	 * @return array
	 * @throws ExpenseException
	 */
	public function initiateTransaction(string $type, string $url = null): array
	{
		return $this->withdrawAmount($type)->processTransaction($type, $url)->processExpenseResponse();
	}

	private function initialTransactionLogForBills($type): void
	{
		$this->logBillsTransactions(
			$this->credentials['account_id'] ?? null,
			$this->credentials['wallet_id'] ?? null,
			$this->credentials['amount'],
			$this->walletResponse['data']['transaction']['reference'] ?? $this->reference,
			$type,
			$this->credentials['wallet_type'],
			$this->credentials['account_number'] ?? $this->credentials['smartcard_number'] ?? $this->credentials['phone']
		);
	}

	public function getBillTransaction(string $reference)
	{
		$url = config('expense.expense.base_url') . "/bills/{$reference}";
		return sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
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
