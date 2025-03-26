<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image' => ['required', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'profile_image.required' => 'プロフィール画像を設定してください',
            'profile_image.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
            'profile_image.max' => '2MB以下の画像を設定してください',
        ];
    }
}
