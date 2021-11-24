<?php

namespace Credpal\Expense\Http\Requests\Baxi;

use Illuminate\Foundation\Http\FormRequest;

class ElectricityRequest extends FormRequest
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
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric',
            'recipient_number' => 'required|string|regex:/[0-9]/',
            'service_type' => 'required|string',
            'reference' => 'nullable|string',
        ];
    }
}
