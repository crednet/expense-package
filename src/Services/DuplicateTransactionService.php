<?php

namespace Credpal\Expense\Services;

use App\BillTransaction;
use Carbon\Carbon;
use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Symfony\Component\HttpFoundation\Response;

class DuplicateTransactionService
{
    public static function checkDuplicateTransaction($transactionType, $recipientNumber, $data)
    {
        $paymentMethod = $data['wallet_type'] === ENUM::CREDIT ? 'credpal_card' : 'credpal_cash';

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
            ->where('created_at', '>', Carbon::now()->subMinutes(3))
            ->first();

        if ($billsTransaction) {
            throw new ExpenseException("A transaction with similar detail exist. Please try again in few minutes", Response::HTTP_PRECONDITION_FAILED);
        }
    }
}