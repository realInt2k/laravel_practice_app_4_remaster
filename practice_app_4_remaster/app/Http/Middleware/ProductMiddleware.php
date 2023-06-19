<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User */
        $authUser = auth()->user();
        $id = $request->id;
        if (!$authUser->isAdmin() && !$authUser->isSuperAdmin() && !$authUser->hasProduct($id)) {
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
