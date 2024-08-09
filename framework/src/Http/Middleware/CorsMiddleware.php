<?php

namespace Trax\Framework\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Fruitcake\Cors\CorsService;
use Trax\Framework\Auth\ClientRepository;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {   
        $response = $next($request);
        
        if (!Str::of($request->path())->startsWith('trax/api/gateway/clients/')) {
            return $response;
        }

        $clientSlug = explode('/', $request->path())[4];

        $client = app(ClientRepository::class)->findBySlugOrFail(
            $clientSlug
        );

        if (!app(CorsService::class)->isCorsRequest($request)) {
            return $response;
        }

        if (empty($client->cors)) {
            return $response;
        }
        
        $response->header('Access-Control-Allow-Origin', $client->cors);
        $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Experience-API-Version, If-Match, If-None-Match');
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Expose-Headers', 'ETag');

        return $response;
    }
}

