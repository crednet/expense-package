<?php

namespace Credpal\Expense\Services;

use Carbon\Carbon;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Symfony\Component\HttpFoundation\Response;

class DuplicateTransactionService
{
    public static function checkDuplicateTransaction(
        string $transactionType,
        string $accountType,
        string $accountId,
        string $recipientNumber,
        array $data
    )
    {
        $configurationModel = config('expense.configuration_model');

        $duplicateTransactionCheckInterval = $configurationModel::value('duplicate_transaction_check_interval', 1);


        $billsTransaction = config('expense.bill_transactions_model')::where('type', $transactionType)
            ->where('user_id', auth()->user()->id)
            ->where('recipient_number', $recipientNumber)
            ->where('payment_method', $accountType)
            ->where('amount', $data['amount'])
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'success');
            })
            ->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval));

        if ($accountType == Enum::WALLET_TYPE_CREDIT) {
            $billsTransaction = $billsTransaction->where('account_id', $accountId)->first();
        } elseif ($accountType == Enum::WALLET_TYPE_CASH) {
            $billsTransaction = $billsTransaction->where('wallet_id', $accountId)->first();
        }

        if ($billsTransaction) {
            throw new ExpenseException(
                "A transaction with similar detail exist. Please try again in few minutes",
                Response::HTTP_PRECONDITION_FAILED
            );
        }
    }

    public static function checkDuplicateTransfer($accountType, $accountId, $amount)
    {
        $configurationModel = config('expense.configuration_model');
        $duplicateTransactionCheckInterval = $configurationModel::value('duplicate_transaction_check_interval', 1);
        $transferModel = config('expense.transfer_model');

        $transfer = $transferModel::where('user_id', auth()->user()->id)
            ->where('amount', $amount)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'success');
            })
            ->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval));

        if ($accountType == Enum::WALLET_TYPE_CREDIT) {
            $transfer = $transfer->where('account_id', $accountId)->first();
        } elseif ($accountType == Enum::WALLET_TYPE_CASH) {
            $transfer = $transfer->where('wallet_id', $accountId)->first();
        }

        if ($transfer) {
            throw new ExpenseException(
                "A transfer with similar detail exist. Please try again in few minutes",
                Response::HTTP_PRECONDITION_FAILED
            );
        }
    }
}
