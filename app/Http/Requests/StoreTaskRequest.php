<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline'    => ['required', 'date'],
            'priority'    => ['required', 'in:low,medium,high,Low,Medium,High'],
            'status'      => ['nullable', 'in:pending,completed,Pending,Completed'],
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'title.required'    => 'Field title wajib diisi.',
            'deadline.required' => 'Field deadline wajib diisi.',
            'deadline.date'     => 'Format deadline tidak valid (contoh: YYYY-MM-DD HH:mm:ss).',
            'priority.required' => 'Field priority wajib diisi.',
            'priority.in'       => 'Priority hanya boleh bernilai: low, medium, atau high.',
            'status.in'         => 'Status hanya boleh bernilai: pending atau completed.',
        ];
    }

    /**
     * Prepare data before validation (normalize lowercase)
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'priority' => $this->priority ? strtolower(trim($this->priority)) : 'medium',
            'status'   => $this->status ? strtolower(trim($this->status)) : 'pending',
        ]);
    }

    /**
     * Handle a failed validation attempt for API requests
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->is('api/*')) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal. Mohon periksa kembali data yang dikirim.',
                'errors'  => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
