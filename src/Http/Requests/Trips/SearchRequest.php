<?php

namespace Credpal\Expense\Http\Requests\Trips;

use Credpal\Expense\Services\TripsService;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'flight_type' => 'required|in:' . TripsService::FLIGHT_INTERNATIONAL . ',' . TripsService::FLIGHT_LOCAL,
            'flight_routes' => 'required|array',
            'flight_routes.*.departure_date' => 'required|date|after:now',
            'flight_routes.*.origin_location_code' => 'required|size:3',
            'flight_routes.*.destination_location_code' => 'required|size:3',
            'flight_passengers' => 'required|array',
            'flight_passengers.*.code' => 'required|string|size:3',
            'flight_passengers.*.quantity' => 'required|integer',
            'flight_airlines' => 'required|array',
            'flight_airlines.*.airline_code' => 'required',
            'flight_airlines.*.excluded' => 'required|bool',
            'flight_classes' => 'required|array',
            'flight_classes.*.name' => 'required|in:economy,first,business,premium'
        ];
    }
}
