<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewsRequest extends FormRequest
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
            'title' => 'string | required',
            'description' => 'string | required',
            'image' => 'string | required',
            'content' => 'string | required',
            'start_date' => 'required',
            'end_date' => 'required',
        ];
    }
}
