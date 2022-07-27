<?php

namespace App\Http\Requests;

use App\Constants\UserSocialType;
use Illuminate\Foundation\Http\FormRequest;

class LoginSocialRequest extends FormRequest
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
            'social_type' => 'required|in:' . UserSocialType::FB_TYPE . ',' . UserSocialType::GOOGLE_TYPE . ',' . UserSocialType::KAKAOTALK_TYPE . ',' . UserSocialType::APPLE_TYPE,
            'social_id' => 'required',
            'fcm_token' => 'required',
            'device' => 'required'
        ];
    }
}
