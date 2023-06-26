<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

trait AccessDenied
{
    function accessDenied($request): JsonResponse|RedirectResponse
    {
        if ($request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to perform this action.',
            ], Response::HTTP_FORBIDDEN);
        } else {
            return redirect()->back()->withErrors([
                config('constants.AUTHENTICATION_ERROR_KEY')
                => 'you don\'t have permission to perform this action!'
            ]);
        }
    }
}
