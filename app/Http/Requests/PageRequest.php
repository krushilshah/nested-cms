<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer|exists:pages,id', 
            'slug' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'The selected parent page does not exist.',
            'slug.required' => 'The slug field is required.',
            'slug.max' => 'The slug must not exceed 255 characters.',
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'content.required' => 'The content field is required.',
        ];
    }
}