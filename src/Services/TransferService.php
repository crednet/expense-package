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

class TransferService implements ExpenseContract
{
    /**
     * @var Collection
     */
    private $credentials;
    /**
     * @var string
     */
    private $reference;
    /**
     * @var array
     */
    private $walletResponse;
    /**
     * @var array
     */
    private $expenseResponse;
    /**
     * @var string
     */
    private $walletType;

    public function __construct(Collection $credentials)
    {
        $this->credentials = $credentials;
        $this->walletType = $credentials['wallet_type'];
        $this->reference = $credentials['reference'] ?? getReference();
    }


    /**
     * @throws ExpenseException
     * If failure here then notify the user by the exception. If success then call the next function to communicate
     * with the expense service
     */
    private function withdrawAmount(): TransferService
    {
        $walletUrl = ($this->walletType === Enum::DEBIT) ?
            config('expense.debit_wallet_url') :
            config('expense.credit_wallet_url');
        $requestBody = [
            'wallet_id' => $this->credentials['wallet_id'],
            'cbs_account_number' => $this->credentials['wallet_account_number'] ?? null,
            'category' => Enum::TRANSFER,
            'amount' => $this->credentials['amount'],
            'reference' => $this->reference,
            'description' => $this->credentials['description'] ?? 'transfer'

        ];
        $this->walletResponse =  sendRequestAndThrowExceptionOnFailure(
            $walletUrl,
            $requestBody,
            getPrivateKey(Enum::WALLET)
        );
        return $this;
    }


    /**
     * @throws ExpenseException
     */
    private function makeTransfer(): TransferService
    {
        $transferUrl = config('expense.transfer_url');
        $bvnModel = config('expense.bvn_model');
        $bvnColumn = config('expense.bvn_column');
        $bvnInstance = new $bvnModel();
        $requestBody = [
            'bvn' => $bvnInstance->query()->whereUserId($this->credentials['user_id'])->firstOrFail()->{$bvnColumn},
            'amount' => $this->walletResponse['data']['amount'],
            'name' => $this->credentials['name'],
            'account_number' => $this->credentials['account_number'],
            'bank_code' => $this->credentials['bank_code'],
            'description' => $this->walletResponse['data']['description'] ?? 'transfer',
            'reference' => $this->walletResponse['data']['reference'] ?? $this->reference
        ];
        $this->expenseResponse = sendRequestTo($transferUrl, $requestBody, getPrivateKey(Enum::EXPENSE));
        return $this;
    }

    /**
     * @throws ExpenseException
     * If request fails at the point of trying to initiate a transfer then immediately update the account service
     * However, if it is successful return response to the user
     */
    private function processExpenseResponse(): array
    {
        $this->reverseWalletAndNotifyUserIfTransferFailedOnInitiation($this->expenseResponse);
        return $this->notifyUserOnSuccessfulTransferInitiation($this->expenseResponse);
    }

    /**
     * @param array $expenseResponse
     * @throws ExpenseException
     */
    private function reverseWalletAndNotifyUserIfTransferFailedOnInitiation(array $expenseResponse): void
    {
        $status = $expenseResponse['status'];
        $reference = $expenseResponse['data']['reference'];
        if (!$status) {
            // update to reverse wallet if the transfer failed
            $walletUpdateUrl = ($this->walletType === Enum::DEBIT) ?
                config('expense.debit_wallet_finalize_url') :
                config('expense.credit_wallet_finalize_url');
            $requestBody = [
                'reference' => $reference,
                'status' => Enum::FAILED
            ];
            // There is an edge case here. Assuming the wallet service was not available at this point. It means
            // it wont be updated/reversed for a while. So that is why jobs and queues should be used,
            // So that whenever it comes online it can pick jobs from the queue
            sendRequestTo($walletUpdateUrl, $requestBody, getPrivateKey(Enum::WALLET));
        }
    }

    /**
     * @param array $expenseResponse
     * @return array
     * @throws ExpenseException
     */
    private function notifyUserOnSuccessfulTransferInitiation(array $expenseResponse): array
    {
        $status = $expenseResponse['status'];
        if (!$status) {
            // Notify the user here
            throw new ExpenseException(
                trans('expense::exception.unsuccessful_transaction'),
                Response::HTTP_PRECONDITION_FAILED
            );
        }
        return $expenseResponse;
    }

    public function initiateTransaction(): array
    {
        return $this->withdrawAmount()->makeTransfer()->processExpenseResponse();
    }

    /**
     * @param string $reference
     * @param string $status
     * @return array
     * @throws ExpenseException
     */
    public static function finalizeTransaction(string $reference, string $status): array
    {
        $debitWalletUrl = config('expense.debit_wallet_finalize_url');
        $creditWalletUrl = config('expense.credit_wallet_finalize_url');
        $requestBody = [
            'reference' => $reference,
            'status' => $status
        ];
        //will send request to both since at this point there is no way to figure out which wallet type
        $debitWalletResponse = sendRequestTo($debitWalletUrl, $requestBody, getPrivateKey(Enum::WALLET));
        $creditWalletResponse = sendRequestTo($creditWalletUrl, $requestBody, getPrivateKey(Enum::WALLET));
        if (!$debitWalletResponse['status'] || !$creditWalletResponse['status']) {
            throw new ExpenseException(
                trans('expense::exception.unsuccessful_webhook'),
                Response::HTTP_PRECONDITION_FAILED
            );
        }
        return $requestBody;
    }
}
