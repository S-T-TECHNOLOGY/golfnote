<?php

namespace App\Http\Requests;

use App\Exceptions\BusinessException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required',
            'account_name' => 'required',
            'phone' => 'required',
            'gender' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $failedRules = $validator->failed();
        if (isset($failedRules['email']['Unique'])) {
            throw  new BusinessException('Email đã được đăng ký', 'email_exists');
        }
        parent::failedValidation($validator);
    }

}
