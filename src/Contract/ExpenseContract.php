<?php

namespace Credpal\Expense\Contract;

use Credpal\Expense\Exceptions\ExpenseException;

interface ExpenseContract
{
    /**
     * @throws ExpenseException
     */
    public function initiateTransaction(): array;

    /**
     * @param string $reference
     * @param string $status
     * @return array
     */
    public static function finalizeTransaction(string $reference, string $status): array;
}
