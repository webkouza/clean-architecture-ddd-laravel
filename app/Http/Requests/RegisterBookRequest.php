<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 📝 従来のLaravelバリデーション（FormRequest）
 *
 * 特徴:
 * - HTTPリクエストのバリデーション
 * - Laravelフレームワークに依存
 * - プレゼンテーション層の責任
 */
class RegisterBookRequest extends FormRequest
{
    /**
     * リクエストの認可
     */
    public function authorize(): bool
    {
        return true; // 今回は認可チェックなし
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'author' => 'required|string|max:100',
            'isbn' => 'required|string|regex:/^[0-9]{10}$|^[0-9]{13}$/',
        ];
    }

    /**
     * エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは200文字以内で入力してください',
            'author.required' => '著者は必須です',
            'author.max' => '著者は100文字以内で入力してください',
            'isbn.required' => 'ISBNは必須です',
            'isbn.regex' => 'ISBNは10桁または13桁の数字で入力してください',
        ];
    }
}
