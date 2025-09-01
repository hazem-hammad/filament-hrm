<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecureFilename implements ValidationRule
{
    /**
     * Dangerous file extensions
     */
    protected array $dangerousExtensions = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'php3', 'php4', 'php5', 'phtml',
        'asp', 'aspx', 'jsp', 'cfm', 'cgi', 'pl', 'sh', 'py', 'rb', 'go', 'bin', 'run', 'app'
    ];

    /**
     * Suspicious patterns in filenames
     */
    protected array $suspiciousPatterns = [
        '/\.\./',           // Directory traversal
        '/[<>:"|?*]/',      // Invalid filename characters
        '/^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])(\.|$)/i', // Windows reserved names
        '/^\s+|\s+$/',      // Leading/trailing spaces
        '/\x00/',           // Null bytes
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        // Check filename length
        if (strlen($value) > 255) {
            $fail('The :attribute filename is too long.');
            return;
        }

        // Check for empty filename
        if (trim($value) === '') {
            $fail('The :attribute filename cannot be empty.');
            return;
        }

        // Check for suspicious patterns
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail('The :attribute filename contains invalid characters or patterns.');
                return;
            }
        }

        // Check file extension
        $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        if (in_array($extension, $this->dangerousExtensions)) {
            $fail('The :attribute has a potentially dangerous file extension.');
            return;
        }

        // Check for multiple extensions (like file.jpg.php)
        $parts = explode('.', $value);
        if (count($parts) > 3) { // filename.ext1.ext2 is suspicious
            $fail('The :attribute filename has too many extensions.');
            return;
        }

        // Check each part for dangerous extensions
        for ($i = 1; $i < count($parts); $i++) {
            if (in_array(strtolower($parts[$i]), $this->dangerousExtensions)) {
                $fail('The :attribute filename contains dangerous extensions.');
                return;
            }
        }
    }
}