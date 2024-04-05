<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    //response()->json(['error' => 'ahaUnauthenticated.']);
    protected function unauthenticated($request, AuthenticationException $exception)
    {
    //     $token = $request->header('Authorization');

    //     if ($exception->guards()[0] === 'api') {
    //         return response()->json([
    //             'error' => 'Unauthenticated.',
    //             'token' => $request->bearerToken(),
    //         ], Response::HTTP_UNAUTHORIZED);
    //     }

    //     return response()->json([
    //         'error' => 'The token wasn\'t sent or is expired.',
    //         'token' => $request->header('Authorization'),
    //     ]);
    return response()->json([
        'error' => 'Unauthenticated.',
        'token' => $request->bearerToken(), // You may need to adjust this based on your middleware logic
    ], Response::HTTP_UNAUTHORIZED);
    }
}
