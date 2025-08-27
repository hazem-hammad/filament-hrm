<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedForApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('api')->user()->email_verified_at) {
            return (new ErrorResponse(__('Account is not verified'), [], Response::HTTP_UNAUTHORIZED))->toJson();
        }

        return $next($request);
    }
}
