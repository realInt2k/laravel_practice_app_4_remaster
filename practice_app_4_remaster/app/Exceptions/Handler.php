<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        if (method_exists($exception, 'getMessage')) {
            $message = $exception->getMessage();
        } else {
            $message = null;
        }

        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = $message ?? 'Unauthorized';
                break;
            case 403:
                $response['message'] = $message ?? 'Forbidden';
                break;
            case 404:
                $response['message'] = $message ?? 'Not Found';
                break;
            case 405:
                $response['message'] = $message ?? 'Method Not Allowed';
                break;
            default:
                $response['message'] = ($statusCode == 500) ? $message ??
                    'Whoops, looks like something went wrong' : $exception->getMessage();
                break;
        }

        $response['status'] = $statusCode;

        return response()->json($response, $statusCode);
    }

    private function handleApiException($request, Exception $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            // dd('responseException');
            $exception = $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            // dd('AuthenticationException');
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            // dd('ValidationException');
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }
        return $this->customApiResponse($exception);
    }

    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
