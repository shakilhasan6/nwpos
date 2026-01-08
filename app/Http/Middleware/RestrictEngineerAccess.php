<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictEngineerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'engineer') {
            abort(403, 'Access denied for engineers');
        }
        return $next($request);
    }
}
