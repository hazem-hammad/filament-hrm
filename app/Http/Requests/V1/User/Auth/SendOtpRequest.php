<?php

namespace App\Http\Requests\V1\User\Auth;

use App\Enum\OtpActions;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendOtpRequest extends FormRequest
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
            'identifier' => array_merge(
                ['required', 'max:255', 'string'],
                $this->input('type') === OtpActions::VERIFY_EMAIL->value
                    ? ['email', Rule::unique('users', 'email')->whereNull('deleted_at')]
                    : [$this->identifierExistsRule()]
            ),
            'type' => ['required', Rule::in([
                OtpActions::RESET_PASSWORD->value,
                OtpActions::CHANGE_EMAIL->value,
                OtpActions::VERIFY_EMAIL->value,
            ])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'identifier.required' => __('validation.required', ['attribute' => __('validation.attributes.identifier')]),
            'identifier.string' => __('validation.string', ['attribute' => __('validation.attributes.identifier')]),
            'identifier.max' => __('validation.max.string', ['attribute' => __('validation.attributes.identifier'), 'max' => 255]),
            'identifier.email' => __('validation.email', ['attribute' => __('validation.attributes.identifier')]),
            'identifier.unique' => __('validation.unique', ['attribute' => __('validation.attributes.identifier')]),
            'type.required' => __('validation.required', ['attribute' => 'type']),
            'type.in' => __('validation.in', ['attribute' => 'type']),
        ];
    }

    public function attributes(): array
    {
        return [
            'identifier' => __('validation.attributes.identifier'),
        ];
    }

    private function identifierExistsRule(): \Closure
    {
        return function (string $attribute, $value, \Closure $fail) {
            $query = User::query()->whereNull('deleted_at');

            $exists = filter_var($value, FILTER_VALIDATE_EMAIL)
                ? $query->where('email', $value)->exists()
                : $query->where('username', $value)->exists();

            if (! $exists) {
                $fail(__('validation.custom.identifier.invalid_format'));
            }
        };
    }
}
