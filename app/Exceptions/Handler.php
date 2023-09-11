<?php

namespace App\Exceptions;

use Throwable;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Exceptions\ProductIsNotSellableException;
use App\Exceptions\ProductHasInsufficientStock;

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

        $this->renderable(function (ValidationException $e, Request $request) {
            return $request->is('api/*')
                ? response()->json(['error' => $e->getMessage(), 'data' => $e->errors()], $e->status)
                : $e->getResponse();
        });

        // $this->renderable(function (ProductIsNotSellableException $e, Request $request) {
        //     return $request->is('api/*')
        //         ? response()->json(['error' => $e->getMessage(), 'input' => $request->all()], 422)
        //         : $e->getResponse();
        // });

        // $this->renderable(function (ProductHasInsufficientStock $e, Request $request) {
        //     return $request->is('api/*')
        //         ? response()->json(['error' => $e->getMessage(), 'input' => $request->all()], 422)
        //         : $e->getResponse();
        // });
    }

    // public function render($request, Throwable $e)
    // {
    //     return parent::render($request, $e);
    // }
}
