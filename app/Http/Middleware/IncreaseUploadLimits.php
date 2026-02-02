<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreaseUploadLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to increase limits (may not work if PHP was already initialized)
        @ini_set('upload_max_filesize', '25M');
        @ini_set('post_max_size', '25M');
        @ini_set('max_execution_time', '300');
        @ini_set('max_input_time', '300');

        return $next($request);
    }
}
