<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Log;

class RecaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip reCAPTCHA validation if disabled or in testing
        if (!config('recaptcha.enabled') || config('recaptcha.skip_testing')) {
            return $next($request);
        }

        // Only validate POST requests
        if (!$request->isMethod('POST')) {
            return $next($request);
        }

        $recaptchaResponse = $request->input('g-recaptcha-response');
        
        if (empty($recaptchaResponse)) {
            return $this->failedValidation($request, 'reCAPTCHA verification is required.');
        }

        $recaptcha = new ReCaptcha(config('recaptcha.secret_key'));
        
        // For v3, include action verification
        if (config('recaptcha.version') === 'v3') {
            $response = $recaptcha
                ->setExpectedAction(config('recaptcha.action'))
                ->setScoreThreshold(config('recaptcha.score_threshold'))
                ->verify($recaptchaResponse, $request->ip());
        } else {
            $response = $recaptcha->verify($recaptchaResponse, $request->ip());
        }

        if (!$response->isSuccess()) {
            Log::warning('reCAPTCHA validation failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'errors' => $response->getErrorCodes(),
                'url' => $request->fullUrl(),
                'version' => config('recaptcha.version'),
            ]);

            return $this->failedValidation($request, 'reCAPTCHA verification failed. Please try again.');
        }

        // For reCAPTCHA v3, check the score
        if (config('recaptcha.version') === 'v3') {
            $score = $response->getScore();
            $threshold = config('recaptcha.score_threshold', 0.5);
            
            if ($score < $threshold) {
                Log::warning('reCAPTCHA score too low', [
                    'ip' => $request->ip(),
                    'score' => $score,
                    'threshold' => $threshold,
                    'action' => $response->getAction(),
                    'url' => $request->fullUrl(),
                ]);

                return $this->failedValidation($request, 'Security verification failed. Please try again.');
            }
        }

        return $next($request);
    }

    /**
     * Handle failed reCAPTCHA validation
     */
    private function failedValidation(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => ['recaptcha' => [$message]]
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['recaptcha' => $message])
            ->withInput($request->except(['g-recaptcha-response', '_token']));
    }
}
