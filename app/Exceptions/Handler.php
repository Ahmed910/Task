<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    //    protected function unauthenticated($request, AuthenticationException $exception)
    //    {
    //        return redirect()->guest('login');
    //    }

    public function render($request, $exception)
    {
        if ($request->expectsJson()) {
            return match (true) {
                $exception instanceof PostTooLargeException =>  $this->apiResource(code: 400, status: false, message: $exception->getMessage()),
                $exception instanceof AuthenticationException =>  $this->apiResource(code: 401, status: false, message: $exception->getMessage()),
                $exception instanceof ThrottleRequestsException =>  $this->apiResource(code: 429, status: false, message: $exception->getMessage()),
                $exception instanceof ModelNotFoundException ||
                    $exception instanceof NotFoundHttpException  =>  $this->apiResource(code: 404, status: false, message: $exception->getMessage()),
                $exception instanceof ValidationException =>  $this->invalidJson($request, $exception),
                default => $this->apiResource(code: 500, status: false, message: $exception->getMessage() . " in " . $exception->getFile() . " at line " . $exception->getLine())
            };
        }
        return parent::render($request, $exception);
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->apiResource(data: $exception->validator?->errors()?->toArray(), status: false, code: $exception->status, message: $exception->getMessage());
    }
}
