<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled via settings
        $maintenanceMode = get_setting('maintenance_mode', false);

        // If maintenance mode is enabled and this is an API request
        if ($maintenanceMode && $request->is('api/*')) {
            return (new ErrorResponse(
                __('service_temporarily_unavailable'),
                null,
                Response::HTTP_SERVICE_UNAVAILABLE
            ))->toJson();
        }

        return $next($request);
    }
}
