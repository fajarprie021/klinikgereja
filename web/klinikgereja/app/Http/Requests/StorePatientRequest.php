<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_number' => ['required', 'string', 'max:50', 'unique:patients,patient_number'],
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:50', 'unique:patients,nik'],
            'gender' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'patient_type' => ['required', 'in:JEMAAT,UMUM'],
            'church_member_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
