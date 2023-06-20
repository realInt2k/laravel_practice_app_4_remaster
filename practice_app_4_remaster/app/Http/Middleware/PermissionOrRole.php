<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionOrRole
{
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
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to perform this action.',
                ], Response::HTTP_FORBIDDEN);
            } else {
                return redirect()->back()->with(
                    config('constants.authenticationErrorKey'),
                    'you don\'t have permission to perform this action!'
                );
            }
        }
        return $next($request);
    }
}
