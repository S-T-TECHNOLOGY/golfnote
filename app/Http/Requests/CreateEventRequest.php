<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'name' => 'required',
            'address' => 'required',
            'quantity' => 'required',
            'join_fee' => 'required',
            'host' => 'required',
            'organizational_unit' => 'required',
            'caddie_fee' => 'required',
            'green_fee' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'image' => 'required'
        ];
    }
}
