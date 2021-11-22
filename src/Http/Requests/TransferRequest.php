<?php

namespace  Credpal\Expense\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
             "wallet_type" => ["required", "string"],
             "amount" => ["required", "numeric"],
             "cbs_account_number" => ["nullable", "numeric"],
             "account_number" => ["required", "numeric"],
             "account_name" => ["required", "string"],
             "bank_code" => ["required", "numeric"],
             "description" => ["nullable", "string"],
         ];
    }
}
