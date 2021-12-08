<?php

namespace Credpal\Expense\Http\Requests\Baxi;

use Illuminate\Foundation\Http\FormRequest;

class MutltichoiceBundleRequest extends FormRequest
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
            'smartcard_number' => 'required|string|regex:/[0-9]/',
            'total_amount' => 'required|numeric',
            'product_code' => 'required|string',
            'product_monthsPaidFor' => 'required|numeric',
            'addon_code' => 'nullable|string',
            'addon_monthsPaidFor' => 'nullable|numeric',
            'service_type' => 'required|string|in:dstv,gotv,startimes',
            'reference' => 'nullable|string',
        ];
    }
}
