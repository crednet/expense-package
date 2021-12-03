<?php

namespace Credpal\Expense\Http\Requests\Trips;

use Credpal\Expense\Services\ExpenseProcess;
use Credpal\Expense\Services\TripsService;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $confirmTicketRequest = new ConfirmTicketPriceRequest();
        $rules = array_merge($confirmTicketRequest->rules(), [
            'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_method' => ['required', Rule::in(ExpenseProcess::PAYMENT_METHOD_CASH, ExpenseProcess::PAYMENT_METHOD_CREDIT_CARD)],
            'wallet_type' => ['required', Rule::in(Enum::DEBIT, Enum::CREDIT)],
            'wallet_id' => ['required_if:wallet_type,' . Enum::DEBIT],
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(TripsService::TYPE_FLIGHT)],
            'account_id' => [
                'required_if:wallet_type,' . Enum::CREDIT,
                Rule::exists('personal_card_accounts', 'id')->where(function ($query) {
                    $query->where('user_id', $this->input('user_id'));
                }),
            ],
        ]);

        return $rules;
    }
}
