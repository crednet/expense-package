<?php

namespace Credpal\Expense\Http\Requests\Baxi;

class DataBundleRequest
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
            'datacode' => 'required',
            'reference' => 'nullable|string',
        ];
    }
}
