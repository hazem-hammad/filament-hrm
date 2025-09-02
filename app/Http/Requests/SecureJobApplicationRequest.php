<?php

namespace App\Http\Requests;

use App\Models\Job;
use App\Rules\NoSuspiciousContent;
use App\Rules\SafeUrl;
use ReCaptcha\ReCaptcha;

class SecureJobApplicationRequest extends SecureFormRequest
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
        $rules = [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', new NoSuspiciousContent],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', new NoSuspiciousContent],
            'email' => ['required', 'email:rfc,dns', 'max:255', new NoSuspiciousContent],
            'phone' => ['required', 'string', 'max:11', 'regex:/^(010|011|012|015)\d{8}$/', new NoSuspiciousContent],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png,gif,txt,csv', 'max:5120'],
            'linkedin_url' => ['required', 'url', 'max:255', 'regex:/^https?:\/\/(www\.)?linkedin\.com\/in\/[a-zA-Z0-9-]+\/?$/', new SafeUrl],
        ];

        // Add custom question validation if job exists
        $slug = $this->route('slug');
        if ($slug) {
            $job = Job::where('slug', $slug)->with('customQuestions')->first();
            if ($job && $job->customQuestions) {
                foreach ($job->customQuestions as $question) {
                    $fieldName = "custom_questions.{$question->id}";
                    $fieldRules = [];

                    if ($question->is_required) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }

                    switch ($question->type) {
                        case 'text_field':
                            $fieldRules[] = 'string';
                            $fieldRules[] = 'max:500';
                            // Basic XSS protection
                            $fieldRules[] = 'regex:/^[^<>]*$/';
                            break;
                        case 'textarea':
                            $fieldRules[] = 'string';
                            $fieldRules[] = 'max:2000';
                            // Allow basic formatting but block script tags
                            $fieldRules[] = 'regex:/^(?!.*<script).*$/i';
                            break;
                        case 'date':
                            $fieldRules[] = 'date';
                            $fieldRules[] = 'before:today';
                            break;
                        case 'file_upload':
                            $fieldRules[] = 'file';
                            $fieldRules[] = 'mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png,gif,txt,csv';
                            $fieldRules[] = 'max:5120'; // 5MB
                            break;
                        case 'toggle':
                            $fieldRules[] = 'boolean';
                            break;
                        case 'multi_select':
                            $fieldRules[] = 'array';
                            $fieldRules[] = 'max:10'; // Limit selections
                            break;
                    }

                    $rules[$fieldName] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Rate limiting check
            $this->checkRateLimit($validator);

            // Honeypot check
            $this->checkHoneypot($validator);
        });
    }


    /**
     * Check for rate limiting
     */
    private function checkRateLimit($validator)
    {
        $key = 'job_application:' . $this->ip();
        $attempts = cache()->get($key, 0);
        $maxAttempts = 10; // 10 attempts per hour (increased from 5)
        $decayMinutes = 60;

        if ($attempts >= $maxAttempts) {
            $validator->errors()->add('rate_limit', 'Too many application attempts. Please try again later.');
        } else {
            cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        }
    }

    /**
     * Check honeypot field for bots
     */
    private function checkHoneypot($validator)
    {
        $honeypot = $this->input('website'); // Hidden field that should be empty

        if (!empty($honeypot)) {
            $validator->errors()->add('security', 'Security check failed.');

            \Log::warning('Honeypot triggered', [
                'ip' => $this->ip(),
                'honeypot_value' => $honeypot,
            ]);
        }
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'email.email' => 'Please provide a valid email address.',
            'phone.regex' => 'Please provide a valid phone number.',
            'linkedin_url.regex' => 'Please provide a valid LinkedIn profile URL.',
            'resume.max' => 'Resume file size cannot exceed 5MB.',
            'custom_questions.*.regex' => 'Invalid characters detected in your response.',
        ];
    }
}
