<?php

namespace Credpal\Expense\Http\Requests\Trips;

use Illuminate\Foundation\Http\FormRequest;

class FlightRulesRequest extends FormRequest
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
        return [
            'agent_id' => ['required', 'integer'],
            'session_id' => ['required'],
            'combination_id' => ['required', 'integer'],
            'recommendation_id' => ['required', 'integer'],
            'g_d_s_id' => ['required', 'integer'],
            'flight_route_index' => ['required', 'integer'],
            'passenger_type_code' => ['required', 'string', 'size:3']
        ];
    }
}
