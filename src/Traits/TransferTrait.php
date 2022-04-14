<?php

namespace Credpal\Expense\Traits;

use Credpal\Expense\Utilities\Enum;

trait TransferTrait
{
	public $transfer;

	public function logTransfer(
		$userId,
		$accountId,
		$walletId,
		$walletType,
		$reference,
		$amount
	): void {
		$this->getTransferInstance();

		$this->transfer->initialTransferLoggerForCash(
			$userId,
			$accountId,
			$walletId,
			$walletType,
			$reference,
			$amount,
			ENUM::PENDING,
		);
	}

	public function getTransferInstance()
	{
		$creditCardTransactionModel = config('expense.credit_card_transaction');
		return $this->transfer =
			new $creditCardTransactionModel(
				$this->credentials['amount'] ?? null,
				$this->credentials['account_id'] ?? null,
			);
	}
}
