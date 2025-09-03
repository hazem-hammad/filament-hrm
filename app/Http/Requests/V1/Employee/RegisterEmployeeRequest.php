<?php

namespace App\Http\Requests\V1\Employee;

use App\Enum\MaritalStatus;
use App\Enum\ContractType;
use App\Enum\EmployeeLevel;
use Illuminate\Foundation\Http\FormRequest;

class RegisterEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public registration, no authorization needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Personal Information
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:employees,email', 'max:255'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'business_phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female'],
            'marital_status' => ['required', 'in:' . implode(',', array_keys(MaritalStatus::options()))],
            'national_id' => ['required', 'string', 'max:255', 'unique:employees,national_id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],

            // Company Information
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'employee_level' => ['required', 'in:' . implode(',', array_keys(EmployeeLevel::options()))],
            'contract_type' => ['required', 'in:' . implode(',', array_keys(ContractType::options()))],
            'manager_email' => ['nullable', 'email', 'exists:employees,email'],
            'company_joining_date' => ['nullable', 'date'],

            // Password
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required',
            'email.required' => 'Work email address is required',
            'email.unique' => 'This email address is already registered',
            'phone.required' => 'Phone number is required',
            'gender.required' => 'Gender selection is required',
            'marital_status.required' => 'Marital status is required',
            'national_id.required' => 'National ID number is required',
            'national_id.unique' => 'This National ID is already registered',
            'department.required' => 'Department is required',
            'position.required' => 'Position is required',
            'employee_level.required' => 'Employee level is required',
            'contract_type.required' => 'Contract type is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'date_of_birth.before' => 'Date of birth must be before today',
            'manager_email.exists' => 'Manager email does not exist in the system',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'personal_email' => 'personal email',
            'business_phone' => 'business phone',
            'marital_status' => 'marital status',
            'national_id' => 'national ID',
            'date_of_birth' => 'date of birth',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_relation' => 'emergency contact relation',
            'emergency_contact_phone' => 'emergency contact phone',
            'employee_level' => 'employee level',
            'contract_type' => 'contract type',
            'manager_email' => 'manager email',
            'company_joining_date' => 'company joining date',
            'password_confirmation' => 'password confirmation',
        ];
    }
}