<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'evaluatee_id' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * カスタムエラーメッセージ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rating.required' => '評価を選択してください',
            'rating.integer'  => '評価は数字で入力してください',
            'rating.min'      => '評価は1以上で入力してください',
            'rating.max'      => '評価は5以下で入力してください',
            'evaluatee_id.required' => '評価対象のユーザーが不正です',
            'evaluatee_id.exists'   => '評価対象のユーザーが存在しません',
        ];
    }
}
