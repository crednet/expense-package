<?php

namespace Credpal\Expense\Http\Requests\Baxi;

use Credpal\Expense\Utilities\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AirtimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
			'user_id' => 'required|exists:users,id',
            'recipient_number' => 'required|string|regex:/[0-9]/',
            'amount' => 'required|numeric',
            'service_type' => 'required|string',
            'plan' => 'required|string|in:prepaid,postpaid',
            'reference' => 'nullable|string',
			'wallet_type' => ['required', Rule::in(Enum::DEBIT, Enum::CREDIT)],
			'wallet_id' => ['required_if:wallet_type,' . Enum::DEBIT],
			'account_id' => [
				'required_if:wallet_type,' . Enum::CREDIT,
				Rule::exists('personal_card_accounts', 'id')->where(function ($query) {
					$query->where('user_id', $this->input('user_id'));
				}),
			],
        ];
    }
}
