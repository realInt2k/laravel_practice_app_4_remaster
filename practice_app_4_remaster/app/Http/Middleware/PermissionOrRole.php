<?php

namespace App\Http\Middleware;

use App\Http\Traits\AccessDenied;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionOrRole
{
    use AccessDenied;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $expression): Response
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $permissionCheck = false;
        $roleCheck = false;
        $expression = explode('|', $expression);
        foreach ($expression as $operand) {
            if ($user->hasPermission($operand)) {
                $permissionCheck = true;
                break;
            }
            if ($user->hasRole($operand)) {
                $roleCheck = true;
                break;
            }
        }
        if (!$roleCheck && !$permissionCheck) {
            return $this->accessDenied($request);
        }
        return $next($request);
    }
}
