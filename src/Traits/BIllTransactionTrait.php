<?php

namespace Credpal\Expense\Traits;

use Credpal\Expense\Utilities\Enum;

trait BIllTransactionTrait
{
	public $billsTransaction;

	public function logBillsTransactions(
		$accountId,
		$walletId,
		$amount,
		$reference,
		$type,
		$walletType,
		$recipientNumber
	): void {
		$this->getBillsTransactionInstance();
		$this->billsTransaction::transactionLogger(
			$accountId,
			$walletId,
			$amount,
			$reference,
			$type,
			ENUM::PENDING,
			$walletType === ENUM::CREDIT ? 'credpal_card' : 'credpal_cash',
			$recipientNumber
		);
	}

	public function updateTransactionLog($reference, $status, $description, $data): void
	{
		$this->getBillsTransactionInstance();
		$this->billsTransaction::updateTransactionLog($reference, $status, $description, $data);
	}

	public function getBillsTransactionInstance()
	{
		$billTransactionModel = config('expense.bill_transactions_model');
		$this->billsTransaction = new $billTransactionModel();
		return $this->billsTransaction;
	}
}
