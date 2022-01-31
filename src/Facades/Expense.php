<?php

namespace Credpal\Expense\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed getBillTransaction($reference)
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
