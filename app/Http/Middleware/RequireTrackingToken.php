<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTrackingToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('order_track.api_token');
        $providedToken = $request->header('tracking_token');

        if (
            $configuredToken === ''
            || ! is_string($providedToken)
            || ! hash_equals($configuredToken, $providedToken)
        ) {
            return new JsonResponse([
                'message' => __('tracking.api.invalid_tracking_token'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
