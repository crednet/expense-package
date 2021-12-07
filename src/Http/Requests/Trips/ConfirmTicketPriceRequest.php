<?php

namespace Credpal\Expense\Http\Requests\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Credpal\Expense\Services\TripsService;
use Illuminate\Validation\Rule;

class ConfirmTicketPriceRequest extends FormRequest
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
            'agent_id' => ['required_if:flight_type,' . TripsService::FLIGHT_INTERNATIONAL, 'integer'],
            'session_id' => ['required'],
            'combination_id' => ['required_if:flight_type,' . TripsService::FLIGHT_INTERNATIONAL, 'integer'],
            'recommendation_id' => ['required_if:flight_type,' . TripsService::FLIGHT_INTERNATIONAL, 'integer'],
            'g_d_s_id' => ['required_if:flight_type,' . TripsService::FLIGHT_INTERNATIONAL, 'integer'],
            'selected_flights' => ['required_if:flight_type,' . TripsService::FLIGHT_LOCAL , 'array'],
            'selected_flights.*.combination_i_d' => ['required_if:flight_type,' . TripsService::FLIGHT_LOCAL, 'integer'],
            'selected_flights.*.recommendation_i_d' => ['required_if:flight_type,' . TripsService::FLIGHT_LOCAL, 'integer'],
            'selected_flights.*.agent_id' => ['required_if:flight_type,' . TripsService::FLIGHT_LOCAL, 'integer'],
            'selected_flights.*.gds_i_d' => ['required_if:flight_type,' . TripsService::FLIGHT_LOCAL, 'integer'],
            'selected_flights.*.flight_route_index' => ['required', 'integer'],
            'air_travellers' => ['required', 'array'],
            'air_travellers.*.passenger_type_code' => [
                'required',
                'size:3',
                Rule::in(TripsService::PASSENGER_TYPE_CODE)
            ],
            'air_travellers.*.last_name' => ['required'],
            'air_travellers.*.first_name' => ['required'],
            'air_travellers.*.middle_name' => ['nullable'],
            'air_travellers.*.birth_date' => ['required', 'date'],
            'air_travellers.*.name_prefix' => ['required'],
            'air_travellers.*.gender' => ['required'],
            'air_travellers.*.address' => ['required', 'array'],
            'air_travellers.*.address.contact_name' => ['required'],
            'air_travellers.*.address.address_line_1' => ['required'],
            'air_travellers.*.address.address_line_2' => ['nullable'],
            'air_travellers.*.address.city' => ['required'],
            'air_travellers.*.address.country_code' => ['required'],
            'air_travellers.*.documents' => ['required_if:flight_type,' . TripsService::FLIGHT_INTERNATIONAL, 'array'],
            'air_travellers.*.documents.*.doc_type' => ['in:DOCS,DOCO,DACA'],
            'air_travellers.*.documents.*.issue_location' => [ 'size:3'],
            'air_travellers.*.documents.*.birth_country_code' => ['size:3'],
            'air_travellers.*.documents.*.doc_id' => ['numeric'],
            'air_travellers.*.documents.*.issue_country_code' => ['size:3'],
            'air_travellers.*.documents.*.effective_date' => ['date'],
            'air_travellers.*.documents.*.inner_doc_type' => ['nullable', 'in:visa,passport'],
            'billing_address' => 'required|array',
            'billing_address.*' => [
                'contact_name' => 'required',
                'address_line_1' => 'required',
                'city' => 'required',
                'country_code' => 'required',
                'contact_mobile_no' => 'required',
                'contact_email' => ['required', 'email']
            ]
        ];
    }
}
