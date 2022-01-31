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
	public function getBillTransaction(string $reference);
}
