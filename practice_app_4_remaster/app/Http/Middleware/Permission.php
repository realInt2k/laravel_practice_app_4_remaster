<?php

namespace App\Http\Middleware;

use App\Http\Traits\AccessDenied;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Permission
{
    use AccessDenied;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->user()->hasPermission($permission)) {
            return $this->accessDenied($request);
        }
        return $next($request);
    }
}
