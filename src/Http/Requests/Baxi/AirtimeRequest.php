<?php

namespace Credpal\Expense\Http\Requests\Baxi;

use Illuminate\Foundation\Http\FormRequest;

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
			'bvn' => 'required',
			'recipient_number' => 'required|string|regex:/[0-9]/',
			'amount' => 'required|numeric',
			'service_type' => 'required|string',
			'plan' => 'required|string|in:prepaid,postpaid',
			'reference' => 'nullable|string',
		];
	}
}