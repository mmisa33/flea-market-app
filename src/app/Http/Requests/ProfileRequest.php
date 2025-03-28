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
        $user = $this->user();

        return [
            'name' => ['required'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/', 'size:8'],
            'address' => ['required'],
            'profile_image' => [$user && $user->profile && $user->profile->profile_image ? 'nullable' : 'required', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフン付きの8文字で入力してください',
            'address.required' => '住所を入力してください',
            'profile_image.required' => 'プロフィール画像を設定してください',
            'profile_image.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
            'profile_image.max' => '1MB以下の画像を設定してください',
        ];
    }
}
