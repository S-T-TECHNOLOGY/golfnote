<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserReservationRequest extends FormRequest
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
            'golf_id' => 'required',
            'email' => 'required | email',
            'phone' => 'string | required',
            'total_player' => 'integer | required',
            'user_name' => 'string | required',
            'date' => 'required',
            'note' => 'string'
        ];
    }
}
