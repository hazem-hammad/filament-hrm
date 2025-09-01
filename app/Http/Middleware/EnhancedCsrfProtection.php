<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnhancedCsrfProtection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for API routes and GET requests
        if ($request->is('api/*') || !$request->isMethod('POST')) {
            return $next($request);
        }

        // Enhanced CSRF validation with additional security checks
        if (!$this->validateCsrfToken($request)) {
            $this->logCsrfViolation($request);
            return $this->handleCsrfFailure($request);
        }

        // Check for double-submit cookie pattern
        if (!$this->validateDoubleSubmitCookie($request)) {
            $this->logCsrfViolation($request, 'double_submit_cookie');
            return $this->handleCsrfFailure($request);
        }

        // Validate origin header for additional protection
        if (!$this->validateOrigin($request)) {
            $this->logCsrfViolation($request, 'origin_mismatch');
            return $this->handleCsrfFailure($request);
        }

        return $next($request);
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken(Request $request): bool
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token) {
            return false;
        }

        return hash_equals(
            $request->session()->token(),
            $token
        );
    }

    /**
     * Validate double-submit cookie pattern
     */
    protected function validateDoubleSubmitCookie(Request $request): bool
    {
        $cookieToken = $request->cookie('XSRF-TOKEN');
        $headerToken = $request->header('X-XSRF-TOKEN');

        if (!$cookieToken || !$headerToken) {
            return true; // Skip if not using double-submit pattern
        }

        return hash_equals($cookieToken, $headerToken);
    }

    /**
     * Validate request origin
     */
    protected function validateOrigin(Request $request): bool
    {
        $origin = $request->header('Origin') ?: $request->header('Referer');
        
        if (!$origin) {
            return false;
        }

        $allowedOrigins = [
            config('app.url'),
            $request->getSchemeAndHttpHost(),
        ];

        foreach ($allowedOrigins as $allowedOrigin) {
            if (str_starts_with($origin, $allowedOrigin)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log CSRF violation attempt
     */
    protected function logCsrfViolation(Request $request, string $type = 'token_mismatch'): void
    {
        Log::warning('CSRF protection violation', [
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'referer' => $request->header('Referer'),
            'origin' => $request->header('Origin'),
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Handle CSRF validation failure
     */
    protected function handleCsrfFailure(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                'errors' => ['csrf' => ['Invalid CSRF token']]
            ], 419);
        }

        return redirect()->back()
            ->withErrors(['csrf' => 'CSRF token mismatch. Please refresh the page and try again.'])
            ->withInput($request->except(['_token', '_method']));
    }
}
