<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check if the user has logged in
        if(Auth::check()){
            // get the user that has logged in
            $user = Auth::user();

            // if the user is inactive logg them out and return a 403 fobidden status
            if(!$user->active){

                // log the user out
                Auth::logout();

                $message = 'Access forbidden. Your account is not active';

                // return a json response for api request, abort for web requests
                return $request->expectsJson()
                            ? response()->json(['message' => $message], 403)
                            : abort(403, $message);

            }
        }
        return $next($request);
    }
}
