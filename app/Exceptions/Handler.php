<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
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
            //
        });
    }

    /**
     * Handle unauthenticated requests.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (Route::has('login')) {
            return redirect()->guest(route('login'));
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    /**
     * Forcar resposta JSON para validacoes em rotas de API,
     * mesmo quando o header Accept nao estiver como application/json.
     */
    protected function invalid($request, ValidationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Dados invalidos.',
                'errors' => $exception->errors(),
            ], $exception->status);
        }

        return parent::invalid($request, $exception);
    }
}
