<?php

namespace Credpal\Expense\Contract;

use Credpal\Expense\Exceptions\ExpenseException;

interface ExpenseContract
{
	/**
	 * @param $type
	 * @param $requestBody
	 * @param $url
	 * @return array
	 * @throws ExpenseException
	 */
    public function initiateTransaction($type, $requestBody, $url): array;

    /**
     * @param string $reference
     * @param string $status
     * @return array
     */
    public static function finalizeTransaction(string $reference, string $status): array;
}
