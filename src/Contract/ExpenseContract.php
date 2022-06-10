<?php

namespace Credpal\Expense\Contract;

use Credpal\Expense\Exceptions\ExpenseException;

interface ExpenseContract
{
	/**
	 * @param string $type
	 * @param string $url
	 * @return array
	 * @throws ExpenseException
	 */
	public function initiateTransaction(string $type, string $url): array;

	/**
	 * @param string $reference
	 * @param string $status
	 * @return array
	 */
	public static function finalizeTransaction(string $reference, string $status): array;

	/**
	 * @param string $reference
	 */
	public function getTransferByReference(string $reference);

	/**
	 * @param string $reference
	 */
	public function getBillTransaction(string $reference);

	public function reverseCash(bool $status, string $reference, string $walletId);

	/**
	 * @param bool $status
	 * @param string $reference
	 * @param string|int $accountId
	 * @param float $amount
	 */
	public function reverseCredit(bool $status, string $reference, $accountId, float $amount);
}
