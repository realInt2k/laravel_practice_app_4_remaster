<?php

namespace App\Http\Traits;

use Illuminate\Http\Response;

trait AccessDenied {
    function accessDenied($request) {
        if ($request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to perform this action.',
            ], Response::HTTP_FORBIDDEN);
        } else {
            return redirect()->back()->with(
                config('constants.AUTHENTICATION_ERROR_KEY'),
                'you don\'t have permission to perform this action!'
            );
        }
    }
}