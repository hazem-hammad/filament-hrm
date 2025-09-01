<?php

namespace App\Traits;

trait SanitizesInput
{
    /**
     * Sanitize all input data
     */
    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $sanitized[$key] = $this->sanitizeValue($value);
        }
        
        return $sanitized;
    }

    /**
     * Sanitize a single value
     */
    protected function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $this->sanitizeInput($value);
        }

        if (!is_string($value)) {
            return $value;
        }

        // Basic XSS protection
        $value = $this->removeXssPatterns($value);
        
        // SQL injection protection
        $value = $this->removeSqlInjectionPatterns($value);
        
        // Remove null bytes
        $value = str_replace("\0", '', $value);
        
        // Normalize line endings
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        
        return trim($value);
    }

    /**
     * Remove XSS patterns
     */
    protected function removeXssPatterns(string $value): string
    {
        $patterns = [
            // Script tags
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i',
            
            // Iframe and object tags
            '/<iframe\b[^>]*>/i',
            '/<\/iframe>/i',
            '/<object\b[^>]*>/i',
            '/<\/object>/i',
            '/<embed\b[^>]*>/i',
            '/<\/embed>/i',
            
            // Link and meta tags
            '/<link\b[^>]*>/i',
            '/<meta\b[^>]*>/i',
            
            // Style tags
            '/<style\b[^>]*>(.*?)<\/style>/is',
            '/style\s*=/i',
        ];

        foreach ($patterns as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }

        return $value;
    }

    /**
     * Remove SQL injection patterns
     */
    protected function removeSqlInjectionPatterns(string $value): string
    {
        $patterns = [
            '/\bunion\s+select\b/i',
            '/\bselect\s+.*\bfrom\b/i',
            '/\binsert\s+into\b/i',
            '/\bdelete\s+from\b/i',
            '/\bdrop\s+table\b/i',
            '/\bdrop\s+database\b/i',
            '/\balter\s+table\b/i',
            '/\bcreate\s+table\b/i',
            '/\btruncate\s+table\b/i',
            '/\bexec\s*\(/i',
            '/\bexecute\s*\(/i',
            '/\bsp_\w+/i',
            '/\bxp_\w+/i',
        ];

        foreach ($patterns as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }

        return $value;
    }

    /**
     * Sanitize email input
     */
    protected function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL) ?: '';
    }

    /**
     * Sanitize URL input
     */
    protected function sanitizeUrl(string $url): string
    {
        $url = trim($url);
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        // Ensure URL has a valid scheme
        if (!preg_match('/^https?:\/\//i', $url)) {
            return '';
        }
        
        return $url ?: '';
    }

    /**
     * Sanitize phone number
     */
    protected function sanitizePhone(string $phone): string
    {
        // Remove all non-numeric characters except + and spaces
        return preg_replace('/[^+\d\s]/', '', trim($phone));
    }

    /**
     * Sanitize filename
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove directory traversal attempts
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Limit length
        return substr($filename, 0, 255);
    }

    /**
     * Sanitize HTML content (for rich text fields)
     */
    protected function sanitizeHtml(string $html): string
    {
        // Allow only safe HTML tags and attributes
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>';
        
        return strip_tags($html, $allowedTags);
    }

    /**
     * Check if value contains suspicious patterns
     */
    protected function containsSuspiciousPatterns(string $value): bool
    {
        $patterns = [
            '/(<script[^>]*>.*?<\/script>)/i',
            '/(javascript:|vbscript:|onload=|onerror=)/i',
            '/(\bunion\s+select|\bselect\s+.*\bfrom|\binsert\s+into|\bdelete\s+from|\bdrop\s+table)/i',
            '/(\.\.\/|\.\.\\\\|%2e%2e%2f|%2e%2e%5c)/i',
            '/(<iframe|<object|<embed|<link|<meta)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }
}