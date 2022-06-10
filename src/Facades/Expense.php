<?php

namespace Credpal\Expense\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed getBillTransaction($reference)
 * @method static mixed getTransferByReference($reference)
 * @method static mixed reverseCash(bool $status, string $reference, string $walletId)
 * @method static mixed reverseCredit(bool $status, string $reference, $accountId, float $amount)
 *
 * @see \CredPal\Expense\Contract\ExpenseContract
 */
class Expense extends Facade
{
	/**
	 * @return string
	 */
	protected static function getFacadeAccessor(): string
	{
		return 'expense';
	}
}
