<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => 'Vui lòng chọn đánh giá',
            'rating.min' => 'Đánh giá phải từ 1 đến 5',
            'rating.max' => 'Đánh giá phải từ 1 đến 5',
            'comment.required' => 'Vui lòng nhập nhận xét',
            'comment.string' => 'Nhận xét phải là chuỗi',
            'comment.max' => 'Nhận xét phải có độ dài tối đa là 1000 ký tự',
        ];
    }
}
