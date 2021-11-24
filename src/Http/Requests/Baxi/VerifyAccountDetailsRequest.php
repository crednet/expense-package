<?php

namespace Credpal\Expense\Http\Requests\Baxi;

use Illuminate\Foundation\Http\FormRequest;

class VerifyAccountDetailsRequest extends FormRequest
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
			'service_type' => 'required|string',
			'account_number' => 'required|string',
		];
	}
}
