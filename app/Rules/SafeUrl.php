<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    /**
     * List of allowed URL schemes
     */
    protected array $allowedSchemes = ['http', 'https'];

    /**
     * List of blocked domains/patterns
     */
    protected array $blockedPatterns = [
        'localhost',
        '127.0.0.1',
        '0.0.0.0',
        '10.',
        '192.168.',
        '172.',
        'file://',
        'ftp://',
        'data:',
        'javascript:',
        'vbscript:',
    ];

    public function __construct(array $allowedSchemes = null, array $blockedPatterns = null)
    {
        if ($allowedSchemes !== null) {
            $this->allowedSchemes = $allowedSchemes;
        }

        if ($blockedPatterns !== null) {
            $this->blockedPatterns = array_merge($this->blockedPatterns, $blockedPatterns);
        }
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        // Parse URL
        $parsed = parse_url($value);
        
        if ($parsed === false) {
            $fail('The :attribute must be a valid URL.');
            return;
        }

        // Check scheme
        if (!isset($parsed['scheme']) || !in_array(strtolower($parsed['scheme']), $this->allowedSchemes)) {
            $fail('The :attribute must use a valid scheme (http or https).');
            return;
        }

        // Check for blocked patterns
        foreach ($this->blockedPatterns as $pattern) {
            if (str_contains(strtolower($value), strtolower($pattern))) {
                $fail('The :attribute contains a blocked URL pattern.');
                return;
            }
        }

        // Additional security checks
        if (isset($parsed['host'])) {
            // Check for IP addresses (optional additional security)
            if (filter_var($parsed['host'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                // Allow public IP addresses but could be restricted based on requirements
            }
        }
    }
}