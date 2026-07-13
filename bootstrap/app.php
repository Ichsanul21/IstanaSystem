<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(__DIR__.'/../routes/auth.php');
            Route::middleware('api')->prefix('api/webhook')->name('webhook.')->group(__DIR__.'/../routes/webhook.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'branch' => \App\Http\Middleware\SetBranchContext::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
