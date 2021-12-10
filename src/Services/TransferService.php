<?php

namespace Credpal\Expense\Services;

use App\UserProfile;
use Credpal\Expense\Contract\ExpenseContract;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Traits\ExpenseError;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class TransferService extends ExpenseProcess
{
    protected Collection $credentials;
    protected array $requestBody;

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
        $this->requestBody = [
            'account_name' => $this->credentials['account_name'],
            'account_number' => $this->credentials['account_number'],
            'bank_code' => $this->credentials['bank_code'],
        ];

        return $this->initiateTransaction(ENUM::TRANSFER, $this->requestBody, 'transfers');
    }
}
