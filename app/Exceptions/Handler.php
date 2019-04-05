<?php

namespace App\Exceptions;

use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($request->isMethod('get'))
        {
            if ($exception instanceof MethodNotAllowedHttpException)
            {
                return abort(405);
            }
        }
        if ($request->isMethod('post')) {
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP_METHOD_NOT_ALLOWED',
                    'errors'  => []
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            } else if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP_NOT_FOUND',
                    'errors'  => []
                ], Response::HTTP_NOT_FOUND);
            } else if ($exception instanceof RouteNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP_NOT_FOUND',
                    'errors'  => []
                ], Response::HTTP_NOT_FOUND);
            } else if ($exception instanceof ThrottleRequestsException) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP_TOO_MANY_REQUESTS',
                    'errors'  => []
                ], Response::HTTP_TOO_MANY_REQUESTS);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'line' => $exception->getLine(),
                    'file' => $exception->getFile()
                ], 400);
            }
        }

        return parent::render($request, $exception);
    }
}
