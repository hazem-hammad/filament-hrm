<?php

use App\Http\Responses\ErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prependToGroup('api', \App\Http\Middleware\MaintenanceModeMiddleware::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\ApiSecurityMiddleware::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\ApiKeyMiddleware::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\EnsureUserActive::class);
        $middleware->appendToGroup('api', 'throttle:global');
        $middleware->append(App\Http\Middleware\AppLocalization::class);
        $middleware->alias([
            'is_user_active' => \App\Http\Middleware\EnsureUserActive::class,
            'is_verified' => \App\Http\Middleware\EnsureEmailIsVerifiedForApi::class,
            'rate_limit' => \App\Http\Middleware\SecurityRateLimitMiddleware::class,
            'recaptcha' => \App\Http\Middleware\RecaptchaMiddleware::class,
            'enhanced_csrf' => \App\Http\Middleware\EnhancedCsrfProtection::class,
            'api_security' => \App\Http\Middleware\ApiSecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e) {
            return (new ErrorResponse(collect($e->errors())->first()[0], $e->errors(), $e->status))->toJson();
        });

        $exceptions->render(function (Request $request, Throwable $e) use ($exceptions) {
            if ($request->is('api/*')) {
                $exceptions->respond(function (Response $response) use ($e) {
                    if ($e instanceof ThrottleRequestsException) {
                        return (new ErrorResponse(__('Too Many Attempts.'), null, $response->getStatusCode()))->toJson();
                    }

                    return (new ErrorResponse($e->getMessage(), null, $response->getStatusCode()))->toJson();
                });
            }
        });
    })->create();
