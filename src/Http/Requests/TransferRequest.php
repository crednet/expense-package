<?php

namespace  Credpal\Expense\Http\Requests;

use Credpal\Expense\Utilities\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
         return [
             'user_id' => ["required", "exists:users,id"],
             "wallet_id" => ["required"],
			 'wallet_type' => ["required", Rule::in(Enum::DEBIT)],
             "amount" => ["required", "numeric"],
             "cbs_account_number" => ["nullable", "numeric"],
             "account_number" => ["nullable", "numeric"],
             "account_name" => ["nullable", "string"],
             "bank_code" => ["nullable", "numeric"],
             "description" => ["nullable", "string"],
         ];
    }
}
