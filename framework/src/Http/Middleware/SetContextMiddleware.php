<?php

namespace Trax\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Trax\Framework\Context;
use Trax\Framework\Auth\StoreRepository;
use Trax\Framework\Auth\ClientRepository;
use Trax\Framework\Auth\Authorizer;

class SetContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $service
     * @param  string  $api
     * @param  string  $method
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $service = null, string $api = null, string $method = null)
    {
        // Always reset the context.
        Context::reset();

        // We always have a service, api and method, so we record it in the context.
        Context::setEntryPoint($service, $api, $method);

        $segments = $request->segments();

        // Is the request a page request?
        $isPageRequest = $segments[1] !== 'api';
        
        // Is the request an web request (from the front-end)?
        $isInternalRequest = count($segments) > 3 && $segments[3] === 'front';
        
        // Is the request an external API call?
        $isExternalRequest = !empty($request->route('xclient'));
        
        // Is the request a store request?
        $isStoreRequest = !empty($request->route('xstore'));


        // Set the store in context, after checking it exists.
        // Keep it at the begining as the other context set functions will need the store.
        if ($isStoreRequest) {
            Context::setStore(
                app(StoreRepository::class)->findBySlugOrFail($request->route('xstore'))->slug
            );
        }

        // Set the client in context, after checking it exists.
        if ($isExternalRequest) {
            Context::setClient(
                app(ClientRepository::class)->findBySlugOrFail(
                    $request->route('xclient')
                )
            );       
        }
        
        // Set the user in context if it exists.
        if ($isPageRequest || $isInternalRequest) {
            if ($user = Auth::user()) {
                Context::setUser($user);
            }
        }

        // Check that the store request is authorized on the current consumer.
        if ($isStoreRequest) {
            app(Authorizer::class)->checkConsumerStore(
                $request->route('xstore')
            );
        }

        return $next($request);
    }
}
