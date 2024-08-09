<?php

namespace Trax\Framework\Xapi\Http;

use Illuminate\Support\Facades\Route;

class XapiRoute
{
    /**
     * Define the methods.
     *
     * @param  string  $uri
     * @param  string  $controller
     * @param  array  $middlewares
     * @return void
     */
    public static function allMethods(string $uri, string $controller, array $middlewares = []): void
    {
        Route::post($uri, [$controller, 'post'])->middleware($middlewares);
        Route::put($uri, [$controller, 'put'])->middleware($middlewares);
        Route::get($uri, [$controller, 'get'])->middleware($middlewares);
        Route::delete($uri, [$controller, 'delete'])->middleware($middlewares);
    }
}
