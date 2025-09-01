<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiSecurityMiddleware
{
    /**
     * Suspicious patterns to detect in requests
     */
    protected array $suspiciousPatterns = [
        '/(<script[^>]*>.*?<\/script>)/i',
        '/(javascript:|vbscript:|onload=|onerror=)/i',
        '/(\bunion\s+select|\bselect\s+.*\bfrom|\binsert\s+into|\bdelete\s+from|\bdrop\s+table)/i',
        '/(\.\.\/|\.\.\\\\|%2e%2e%2f|%2e%2e%5c)/i',
        '/(<iframe|<object|<embed|<link|<meta)/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Security validations before processing request
        if (!$this->validateRequestHeaders($request)) {
            $this->logSecurityEvent($request, 'invalid_headers');
            return $this->securityViolationResponse('Invalid request headers');
        }

        if (!$this->validateContentType($request)) {
            $this->logSecurityEvent($request, 'invalid_content_type');
            return $this->securityViolationResponse('Invalid content type');
        }

        if (!$this->validateUserAgent($request)) {
            $this->logSecurityEvent($request, 'suspicious_user_agent');
        }

        if (!$this->scanRequestForThreats($request)) {
            $this->logSecurityEvent($request, 'malicious_payload');
            return $this->securityViolationResponse('Request contains suspicious content');
        }

        // Process the request
        $response = $next($request);

        // Add security headers to response
        $this->addSecurityHeaders($response, $request);
        
        return $response;
    }

    /**
     * Validate request headers for suspicious values
     */
    protected function validateRequestHeaders(Request $request): bool
    {
        $suspiciousHeaders = [
            'X-Forwarded-For',
            'X-Real-IP',
            'X-Originating-IP',
        ];

        foreach ($suspiciousHeaders as $header) {
            $value = $request->header($header);
            if ($value && $this->containsSuspiciousContent($value)) {
                return false;
            }
        }

        // Check for excessively long headers
        foreach ($request->headers->all() as $name => $values) {
            foreach ($values as $value) {
                if (strlen($value) > 8192) { // 8KB limit
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate content type for POST requests
     */
    protected function validateContentType(Request $request): bool
    {
        if (!$request->isMethod('POST')) {
            return true;
        }

        $contentType = $request->header('Content-Type');
        $allowedTypes = [
            'application/json',
            'application/x-www-form-urlencoded',
            'multipart/form-data',
            'text/plain',
        ];

        if (!$contentType) {
            return false;
        }

        foreach ($allowedTypes as $allowedType) {
            if (str_starts_with($contentType, $allowedType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate User-Agent header
     */
    protected function validateUserAgent(Request $request): bool
    {
        $userAgent = $request->userAgent();
        
        if (!$userAgent || strlen($userAgent) < 10) {
            return false;
        }

        // Check for common bot patterns
        $botPatterns = [
            '/bot|crawler|spider|scraper/i',
            '/curl|wget|python|php/i',
        ];

        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scan request for malicious content
     */
    protected function scanRequestForThreats(Request $request): bool
    {
        // Get all request data
        $allData = array_merge(
            $request->query(),
            $request->request->all(),
            $request->headers->all()
        );

        return $this->scanDataRecursively($allData);
    }

    /**
     * Recursively scan data for threats
     */
    protected function scanDataRecursively(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!$this->scanDataRecursively($value)) {
                    return false;
                }
            } elseif (is_string($value)) {
                if ($this->containsSuspiciousContent($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if content contains suspicious patterns
     */
    protected function containsSuspiciousContent(string $content): bool
    {
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add security headers to response
     */
    protected function addSecurityHeaders(Response $response, Request $request): void
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;",
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }
    }

    /**
     * Log security events
     */
    protected function logSecurityEvent(Request $request, string $eventType): void
    {
        Log::warning('API security event detected', [
            'event_type' => $eventType,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Return security violation response
     */
    protected function securityViolationResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'Security violation detected.',
            'error_code' => 'SECURITY_VIOLATION'
        ], 400);
    }
}
