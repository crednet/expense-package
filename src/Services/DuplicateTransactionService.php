<?php

namespace Credpal\Expense\Services;

use Carbon\Carbon;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Symfony\Component\HttpFoundation\Response;

class DuplicateTransactionService
{
	public static function checkDuplicateTransaction(
		string $transactionType,
		string $accountType,
		string $recipientNumber,
		array $data
	)
	{
		$configurationModel = config('expense.configuration_model');

		$duplicateTransactionCheckInterval = $configurationModel::value('duplicate_transaction_check_interval', 5);
		$duplicateTransactionAmountPercentage = $configurationModel::value('duplicate_transaction_amount_percentage', 0.5);

		$billsTransaction = config('expense.bill_transactions_model')::where('type', $transactionType)
			->where('user_id', auth()->user()->id)
			->where('recipient_number', $recipientNumber)
			->where('status', 'pending')
			->whereBetween(
				'amount',
				[
					($data['amount'] - ($data['amount'] * $duplicateTransactionAmountPercentage)),
					($data['amount'] + ($data['amount'] * $duplicateTransactionAmountPercentage))
				]
			)
			->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval));

		if ($accountType === Enum::CREDIT) {
			$billsTransaction = $billsTransaction->where('payment_method', Enum::WALLET_TYPE_CREDIT)
				->where('account_id', $data['account_id'])
				->first();
		} elseif ($accountType === Enum::DEBIT) {
			$billsTransaction = $billsTransaction->where('payment_method', Enum::WALLET_TYPE_CASH)
				->where('wallet_id', $data['wallet_id'])
				->first();
		}

		if ($billsTransaction) {
			throw new ExpenseException(
				"A pending transaction with similar details exists. Please try again in a few minutes",
				Response::HTTP_PRECONDITION_FAILED
			);
		}
	}

	public static function checkDuplicateTransfer($accountType, $accountId, $amount, ?int $userId = null)
	{
		$configurationModel = config('expense.configuration_model');
		$duplicateTransactionCheckInterval = $configurationModel::value('duplicate_transaction_check_interval', 3);
		$transferModel = config('expense.transfer_model');

		$transfer = $transferModel::where('user_id', $userId ?? auth()->user()->id)
			->where('amount', $amount)
			->where(function ($query) {
				$query->where('status', 'pending')
					->orWhere('status', 'success');
			})
			->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval));

		if ($accountType === Enum::WALLET_TYPE_CREDIT) {
			$transfer = $transfer->where('account_id', $accountId)->first();
		} elseif ($accountType === Enum::WALLET_TYPE_CASH) {
			$transfer = $transfer->where('wallet_id', $accountId)->first();
		}

		if ($transfer) {
			throw new ExpenseException(
				"A transfer with similar details exists. Please try again in a few minutes",
				Response::HTTP_PRECONDITION_FAILED
			);
		}
	}
}
