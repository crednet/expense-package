<?php

namespace  Credpal\Expense\Http\Requests;

use Credpal\Expense\Utilities\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
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
             'user_id' => ['required', "exists:users,id"],
             "wallet_id" => ["required"],
			 'wallet_type' => ['required', Rule::in(Enum::DEBIT)],
             "amount" => ["required", "numeric"],
             "cbs_account_number" => ["nullable", "numeric"],
             "account_number" => ["nullable", "numeric"],
             "account_name" => ["nullable", "string"],
             "bank_code" => ["nullable", "numeric"],
             "description" => ["nullable", "string"],
			 "destination" => "nullable|in:personal,merchant,branch,company",
         ];
    }
}
