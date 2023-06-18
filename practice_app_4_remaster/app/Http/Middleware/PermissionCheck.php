<?php

namespace App\Http\Middleware;

use App\Http\Traits\PermissionExpression;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    use PermissionExpression;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $expression): Response
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        if(!$user->isSuperAdmin()) {
            if (!$this->calculate($user, $expression)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You do not have permission to perform this action.',
                    ], 403);
                } else {
                    return redirect()->back();
                }
            }
        }
        return $next($request);
    }
}
