<?php

namespace Trax\Framework\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Trax\Framework\Http\Middleware\Actions\HandleBasicAuth;
use Trax\Framework\Http\Middleware\Actions\HandleXapiVersion;
use Trax\Framework\Auth\ClientRepository;

class ApiMiddleware
{
    use HandleBasicAuth, HandleXapiVersion;

    /**
     * @var array
     */
    protected $supportedGuards = ['basic-http', 'cmi5-token'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {               
        // Get the client to determine the guard.
        $client = app(ClientRepository::class)->findBySlugOrFail(
            $request->route('xclient')
        );

        // Unsupported guard.
        $guard = $client->guard;
        if (!in_array($guard, $this->supportedGuards)) {
            throw new AuthenticationException("API guard not supported: $guard.");
        }

        // Basic Auth.
        if ($guard === 'basic-http' || $guard === 'cmi5-token') {
            $this->handleBasicAuth($client, $request, $next);
        }

        // xAPI version.
        $response = $this->handleXapiVersion($request, $next);
        
        return $response;
    }
}

