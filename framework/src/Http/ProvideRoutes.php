<?php

namespace Trax\Framework\Http;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Trax\Framework\Service\Config;

trait ProvideRoutes
{
    // TO BE DEFINED
    // protected static $serviceKey = 'statements';

    // TO BE DEFINED
    // protected static $routes = [
    //     'users' => ['get' => 'index'],
    // ];

    /**
     * Declare the controller routes.
     *
     * @param  string  $serviceKey
     * @return void
     */
    public static function routes(string $serviceKey = null)
    {
        $serviceKey = isset($serviceKey) ? $serviceKey : static::$serviceKey;

        // Register the routes.
        foreach (static::routesProp() as $endpoint => $methods) {
            foreach ($methods as $httpMethod => $controllerMethod) {

                Route::$httpMethod(
                    "/trax/api/" . $serviceKey . "/$endpoint",
                    [get_class(), $controllerMethod]
                )->middleware('trax.context.restore');
            }
        }
    }

    /**
     * Declare the gateway routes.
     *
     * @param  int $endpointType
     * @param  array  $middlewares
     * @param  string  $serviceKey
     * @return void
     */
    public static function gatewayRoutes(int $endpointType, array $middlewares = [], string $serviceKey = null)
    {
        $serviceKey = isset($serviceKey) ? $serviceKey : static::$serviceKey;

        foreach (static::routesProp() as $endpoint => $methods) {
            foreach ($methods as $httpMethod => $controllerMethod) {
                $api = Str::of($endpoint)->startsWith('stores/{xstore}/')
                    ? Str::of($endpoint)->after('stores/{xstore}/')
                    : $endpoint;

                // Set the context middleware.
                $contextMiddleware = "trax.context.set:$serviceKey,$api,$controllerMethod";

                // Register the WEB route.
                if ($endpointType === Routing::API_AND_WEB || $endpointType === Routing::WEB_ONLY) {
                    $webMiddlewares = array_merge(['web', 'auth', $contextMiddleware], $middlewares);
                    Route::$httpMethod("/trax/api/gateway/front/$endpoint", function (Request $request) use ($controllerMethod, $serviceKey) {
                        return app(
                            Config::serviceClass($serviceKey)
                        )->callFromGateway($request, get_class(), $controllerMethod);
                    })->middleware($webMiddlewares);
                }

                // Register the API route.
                if ($endpointType === Routing::API_AND_WEB || $endpointType === Routing::API_ONLY) {
                    $apiMiddlewares = array_merge([$contextMiddleware, 'trax.api'], $middlewares);
                    Route::$httpMethod("/trax/api/gateway/clients/{xclient}/$endpoint", function (Request $request) use ($controllerMethod, $serviceKey) {
                        return app(
                            Config::serviceClass($serviceKey)
                        )->callFromGateway($request, get_class(), $controllerMethod);
                    })->middleware($apiMiddlewares);
                }
            }
        }
    }

    /**
     * Get the declared routes.
     *
     * @return array
     */
    protected static function routesProp(): array
    {
        return (isset(static::$routes) ? static::$routes : static::buildRoutes());
    }
}
