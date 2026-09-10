<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
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
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline'    => ['sometimes', 'required', 'date'],
            'priority'    => ['sometimes', 'required', 'in:low,medium,high,Low,Medium,High'],
            'status'      => ['sometimes', 'required', 'in:pending,completed,Pending,Completed'],
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'title.required'    => 'Field title wajib diisi jika disertakan.',
            'deadline.required' => 'Field deadline wajib diisi jika disertakan.',
            'deadline.date'     => 'Format deadline tidak valid (contoh: YYYY-MM-DD HH:mm:ss).',
            'priority.in'       => 'Priority hanya boleh bernilai: low, medium, atau high.',
            'status.in'         => 'Status hanya boleh bernilai: pending atau completed.',
        ];
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('priority')) {
            $merge['priority'] = strtolower(trim($this->priority));
        }
        if ($this->has('status')) {
            $merge['status'] = strtolower(trim($this->status));
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
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
