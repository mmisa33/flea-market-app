<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'image_path' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
            'name' => ['required'],
            'brand' => ['required'],
            'price' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'max:255'],
            'condition' => ['required'],
            'category' => ['required'],
            'category.*' => ['exists:categories,id'],
        ];
    }

    /**
     * Get the validation error messages that apply to the request.
     *
     * @return array
     */

    public function messages()
    {
        return [
            'image_path.required' => '商品画像を設定してください',
            'image_path.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
            'image_path.max' => '1MB以下の画像を設定してください',
            'name.required' => '商品名を入力してください',
            'brand.required' => 'ブランド名を入力してください',
            'price.required' => '価格を入力してください',
            'price.integer' => '価格は半角整数で入力してください',
            'price.min' => '価格は0円以上で入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'condition.required' => '商品の状態を選択してください',
            'category.required' => 'カテゴリを選択してください',
            'category.*.exists' => '選択されたカテゴリが存在しません',
        ];
    }
}