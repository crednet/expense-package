<?php

namespace Credpal\Expense\Services;

use App\UserProfile;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Support\Collection;

class TransferService extends ExpenseProcess
{
    protected Collection $credentials;

    public function __construct($credentials)
    {
        parent::__construct($credentials);
    }

    /**
     * @return array
     * @throws ExpenseException
     */
    public function makeTransfer(): array
    {
        $this->setDestinationAccountNumberAndUserBankCode();
        logger('transfering');
        DuplicateTransactionService::checkDuplicateTransfer(
            Enum::WALLET_TYPE_CASH,
            $this->credentials['wallet_id'],
            $this->credentials['amount']
        );
        $this->expenseRequestBody = [
            'account_name' => $this->credentials['account_name'],
            'account_number' => $this->credentials['account_number'],
            'bank_code' => $this->credentials['bank_code'],
        ];

        return $this->initiateTransaction(ENUM::TRANSFER, 'transfers');
    }

    public function withoutAccount()
    {
        return null;
    }

    public function withTransfer(): bool
    {
        return true;
    }

    private function setDestinationAccountNumberAndUserBankCode(): void
    {
        $result = $this->creditCardTransaction->checkBankAccount($this->withoutAccount(), $this->withTransfer());
        $this->credentials['account_number'] = $result['account_number'];
        $this->credentials['bank_code'] = $result['bank_name'];
        $this->credentials['account_name'] = $result['account_name'];
    }
}
