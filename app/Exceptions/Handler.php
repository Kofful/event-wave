<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

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
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json(
                ['errors' => $e->validator->errors()],
                $e->status,
            );
        }

        if ($e instanceof UnauthorizedException) {
            return response(
                [
                    'message' => $e->getMessage() ?: 'Ця дія вимагає авторизації.',
                ],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        if ($e instanceof ModelNotFoundException) {
            return response(
                [
                    'message' => 'Не вдалось знайти нічого за цим ідентифікатором.',
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        if ($e instanceof AuthenticationException || $e instanceof AuthorizationException) {
            return response(
                [
                    'message' => 'Ця дія не доступна цьому користувачу.',
                ],
                Response::HTTP_FORBIDDEN,
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $acceptableMethods = implode(',', $e->getHeaders());

            return response()->json(
                ['message' => "{$request->getMethod()} метод не дозволено для цього ресурсу. Дозволені методи: {$acceptableMethods}."],
                Response::HTTP_METHOD_NOT_ALLOWED,
            );
        }

        if ($e instanceof ServiceUnavailableHttpException) {
            return response(
                [
                    'message' => $e->getMessage() ?: "Виникла помилка під час з'єднання зі стороннім сервісом.",
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (env('APP_DEBUG') === false) {
            return response()->json(
                ['message' => 'Сталася помилка сервера.'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return parent::render($request, $e);
    }
}
