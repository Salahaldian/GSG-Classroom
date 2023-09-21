<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassroomRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('classroom', 0);
        return [
            'name' => ['required', 'string', 'max:255', function($attribute, $value, $fail)
            {
                if ($value == 'admin') {
                    return $fail('This :attribute value is forbidden!');
                }
            }],
            'section' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'room' => "nullable|string|max:255|unique:classrooms,room,$id",
            'cover_image' => [
                'nullable',
                'image',
                'max:1024',
                Rule::dimensions([
                    "min_width" => 200,
                    "min_height" => 200
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return $messages = [
            'required' => 'The :attribute is required!',
            'name.required' => 'The name is required!',
            'cover_image.max' => 'Image size is great than 1M'
        ];
    }
}
