<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'parent';
    }

    public function rules(): array
    {
        return [
            'school_id' => 'required|integer|exists:schools,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:Male,Female,Other',
            'address' => 'required|string',
            'parent_name' => 'required|string|max:255',
            'parent_relation' => 'required|string|in:Father,Mother,Guardian',
            'parent_phone' => 'nullable|string|max:255',
            'parent_email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'document_id' => 'nullable|integer|exists:documents,id',
        ];
    }
}
