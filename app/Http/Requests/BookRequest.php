<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
        // If file is present during update or create request then validate it otherwise ignore it
        if (request()->file() || request()->method() === 'POST') {
            $fileValidation = [
                'image' => 'required|file|mimes:jpg,png,jpeg|dimensions:min_width=600,min_height=600'
            ];
        }

        $stringRule = [
            'required',
            'string',
            'max:150',
        ];
        return array_merge([
            'title' => $stringRule,
            'author' => $stringRule,
            'genre' => $stringRule,
            'description' => [
                'required',
                'string'
            ],
            'isbn' => [
                'required',
                'string',
                'max:13',
                'min:13',
                'unique:books,isbn,' . $this->id
            ],
            'published' => [
                'required',
                'date',
                'before:tomorrow'
            ],
            'publisher' => $stringRule,
        ], $fileValidation ?? []);
    }
}
