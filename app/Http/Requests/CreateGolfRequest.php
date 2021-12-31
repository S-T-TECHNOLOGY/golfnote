<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGolfRequest extends FormRequest
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
            'image' => 'required',
            'phone' => 'required',
            'price' => 'required',
            'time_start' => 'required',
            'time_close' => 'required',
            'description' => 'required',
            'golf_courses' => 'array | required',
        ];
    }
}
