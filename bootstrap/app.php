<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Source\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/web.php'],
        apiPrefix: 'prod/api',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('api')->prefix('api/test')->name('test_api')->group('/var/www/messenger/app/Http/Entities/Chat/Routes/api.php');
            Route::middleware('api')->prefix('api/test')->name('test_api')->group('/var/www/messenger/app/Http/Entities/Message/Routes/api.php');
            Route::middleware('api')->prefix('api/test')->name('test_api')->group('/var/www/messenger/app/Http/Entities/User/Routes/api.php');
            
            Route::middleware('api')->prefix('api/prod')->name('prod_api')->group('/var/www/messenger/app/Http/Entities/Chat/Routes/api.php');
            Route::middleware('api')->prefix('api/prod')->name('prod_api')->group('/var/www/messenger/app/Http/Entities/Message/Routes/api.php');
            Route::middleware('api')->prefix('api/prod')->name('prod_api')->group('/var/www/messenger/app/Http/Entities/User/Routes/api.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        $middleware->web(remove: [
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $throwable) {
            $statusCode = method_exists($throwable, 'getStatusCode') ? $throwable->getStatusCode() : 500;

            return Response::response([
                'Message'=>$throwable->getMessage(),
                'Info'=>[
                    // 'trace'=>$throwable->getTrace(),
                    'line'=>$throwable->getLine(),
                    'file'=>$throwable->getFile(),
                ]
            ], $statusCode);
        });
    })->create();
