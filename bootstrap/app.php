<?php

use App\Http\Middleware\AllowMultipleRoles;
use App\Http\Middleware\OwnerMiddleware;
use App\Http\Middleware\ReceptionistMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'super_admin' => SuperAdminMiddleware::class,
            'owner' => OwnerMiddleware::class,
            'receptionist' => ReceptionistMiddleware::class,
            'roles' => AllowMultipleRoles::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API Route Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'API route not found.',
                    'path' => $request->path(),
                ], 404);
            }
        });

        // Wrong HTTP Method
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request method.',
                ], 405);
            }
        });
    })->create();
