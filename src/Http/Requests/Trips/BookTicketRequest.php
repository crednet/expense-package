<?php

namespace Credpal\Expense\Http\Requests\Trips;

use Illuminate\Foundation\Http\FormRequest;

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
            'wallet_type' => ['required', 'in:debit,credit'],
            'wallet_id' => ['required'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        return $rules;
    }
}
