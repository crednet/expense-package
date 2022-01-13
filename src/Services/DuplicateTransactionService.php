<?php

namespace Credpal\Expense\Services;

use Carbon\Carbon;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Symfony\Component\HttpFoundation\Response;

class DuplicateTransactionService
{
    public static function checkDuplicateTransaction($transactionType, $recipientNumber, $data)
    {
        $paymentMethod = $data['wallet_type'] === Enum::CREDIT ? Enum::WALLET_TYPE_CREDIT : Enum::WALLET_TYPE_CASH;

        $configurationModel = config('expense.configuration_model');

        $duplicateTransactionCheckInterval = $configurationModel::value('duplicate_transaction_check_interval');

        $billsTransaction = config('expense.bill_transactions_model')::where('type', $transactionType)
            ->where('user_id', auth()->user()->id)
            ->where('account_id', $data['account_id'])
            ->where('recipient_number', $recipientNumber)
            ->where('amount', $data['amount'])
            ->where('payment_method', $paymentMethod)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'success');
            })
            ->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval))
            ->first();

        if ($billsTransaction) {
            throw new ExpenseException("A transaction with similar detail exist. Please try again in few minutes", Response::HTTP_PRECONDITION_FAILED);
        }
    }

    public static function checkDuplicateTransfer($accountId, $amount, $data)
    {
        $configurationModel = config('expense.configuration_model');
        $duplicateTransactionCheckInterval = $configurationModel::value('duplicate_transaction_check_interval');
        $transferModel = config('expense.transfer_model');

        $transfer = $transferModel::where('user_id', auth()->user()->id)
            ->where('account_id', $accountId)
            ->where('amount', $amount)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'success');
            })
            ->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval))
            ->first();

        if ($transfer) {
            throw new ExpenseException(
                "A transfer with similar detail exist. Please try again in few minutes",
                Response::HTTP_PRECONDITION_FAILED
            );
        }
    }
}