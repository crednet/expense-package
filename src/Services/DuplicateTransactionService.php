<?php

namespace Credpal\Expense\Services;

use App\Configuration;
use App\Models\Transfer\Transfer;
use Carbon\Carbon;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Symfony\Component\HttpFoundation\Response;

class DuplicateTransactionService
{
    public static function checkDuplicateTransaction($transactionType, $recipientNumber, $data)
    {
        $paymentMethod = $data['wallet_type'] === ENUM::CREDIT ? 'credpal_card' : 'credpal_cash';

        $duplicateTransactionCheckInterval = Configuration::value('duplicate_transaction_check_interval');

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
        $duplicateTransactionCheckInterval = Configuration::value('duplicate_transaction_check_interval');

        $transfer = Transfer::where('user_id', auth()->user()->id)
            ->where('account_id', $accountId)
            ->where('amount', $amount)
            ->where('user_type', $data['user_type'])
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'success');
            })
            ->where('created_at', '>', Carbon::now()->subMinutes($duplicateTransactionCheckInterval))
            ->first();

        if ($transfer) {
            throw new ExpenseException("A transfer with similar detail exist. Please try again in few minutes", Response::HTTP_PRECONDITION_FAILED);
        }
    }
}