<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limiter = 'general'): Response
    {
        $key = $this->resolveRequestSignature($request, $limiter);
        
        if (RateLimiter::tooManyAttempts($key, $this->getMaxAttempts($limiter))) {
            $this->logRateLimitExceeded($request, $limiter);
            return $this->buildRateLimitResponse($request, $key);
        }

        RateLimiter::hit($key, $this->getDecayMinutes($limiter) * 60);

        $response = $next($request);

        return $this->addRateLimitHeaders($response, $key, $limiter);
    }

    /**
     * Resolve the rate limiting key for the request.
     */
    protected function resolveRequestSignature(Request $request, string $limiter): string
    {
        $userId = $request->user()?->id;
        $ip = $request->ip();
        $fingerprint = $this->getRequestFingerprint($request);

        return match($limiter) {
            'job_application' => "job_app:{$ip}:{$fingerprint}",
            'sensitive_forms' => "sensitive:{$ip}:{$userId}:{$fingerprint}",
            'auth' => "auth:{$ip}:{$fingerprint}",
            'api' => "api:{$userId}:{$ip}",
            default => "general:{$ip}:{$fingerprint}"
        };
    }

    /**
     * Get request fingerprint for additional security.
     */
    protected function getRequestFingerprint(Request $request): string
    {
        return hash('sha256', implode('|', [
            $request->userAgent() ?: 'unknown',
            $request->header('Accept-Language') ?: 'unknown',
            $request->header('Accept-Encoding') ?: 'unknown'
        ]));
    }

    /**
     * Get maximum attempts for the limiter.
     */
    protected function getMaxAttempts(string $limiter): int
    {
        return match($limiter) {
            'job_application' => 10,  // 10 applications per hour (increased from 3)
            'sensitive_forms' => 15,  // 15 form submissions per hour (increased from 5)
            'auth' => 10,            // 10 auth attempts per hour (increased from 5)
            'api' => 120,            // 120 API calls per minute (increased from 60)
            default => 60           // 60 general requests per minute (increased from 20)
        };
    }

    /**
     * Get decay minutes for the limiter.
     */
    protected function getDecayMinutes(string $limiter): int
    {
        return match($limiter) {
            'job_application' => 60,  // 1 hour
            'sensitive_forms' => 60,  // 1 hour
            'auth' => 60,            // 1 hour
            'api' => 1,              // 1 minute
            default => 1             // 1 minute
        };
    }

    /**
     * Build rate limit exceeded response.
     */
    protected function buildRateLimitResponse(Request $request, string $key): Response
    {
        $retryAfter = RateLimiter::availableIn($key);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => $retryAfter
            ], 429);
        }

        return response()->view('errors.429', [
            'retryAfter' => $retryAfter
        ], 429);
    }

    /**
     * Add rate limit headers to response.
     */
    protected function addRateLimitHeaders(Response $response, string $key, string $limiter): Response
    {
        $maxAttempts = $this->getMaxAttempts($limiter);
        $remainingAttempts = RateLimiter::remaining($key, $maxAttempts);
        $retryAfter = RateLimiter::availableIn($key);

        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remainingAttempts),
        ]);

        if ($remainingAttempts === 0) {
            $response->headers->add([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
            ]);
        }

        return $response;
    }

    /**
     * Log rate limit exceeded attempts.
     */
    protected function logRateLimitExceeded(Request $request, string $limiter): void
    {
        Log::warning('Rate limit exceeded', [
            'limiter' => $limiter,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
