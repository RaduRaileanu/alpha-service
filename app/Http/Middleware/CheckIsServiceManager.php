<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsServiceManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if the user is not logged in or is not a service manager, return an "access forbidden" message
        if(!Auth::check() || !Auth::user()->hasRole('service_manager')){
            
            $message = 'Access forbidden. Your are not a service manager';
            // return a json response for api request, abort for web requests
            return $request->expectsJson()
                        ? response()->json(['message' => $message], 403)
                        : abort(403, $message);
        }

        
        return $next($request);
    }
}
