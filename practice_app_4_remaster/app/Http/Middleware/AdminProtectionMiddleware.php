<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Traits\AccessDenied;
use Symfony\Component\HttpFoundation\Response;

class AdminProtectionMiddleware
{
    use AccessDenied;
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $targetUser = $this->userService->getById($request->id);
        $action = $request->route()->getName();
        if(!auth()->canPerformAction($action, $targetUser)) {
            return $this->accessDenied($request);
        }
        return $next($request);
    }
}
