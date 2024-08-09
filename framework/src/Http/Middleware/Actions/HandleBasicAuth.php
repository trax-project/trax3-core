<?php

namespace Trax\Framework\Http\Middleware\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Auth\AuthenticationException;
use Trax\Framework\Xapi\Exceptions\XapiAuthenticationException;
use Trax\Framework\Auth\Client;

trait HandleBasicAuth
{
    /**
     * Handle a Basic Auth request.
     *
     * @param  \Trax\Framework\Auth\Client  $client
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function handleBasicAuth(Client $client, Request $request)
    {
        // Get Authorization header.
        if ($request->hasHeader('Authorization')) {
            $authorization = $request->header('Authorization');
        } elseif ($request->has('method') && $request->has('Authorization')) {
            $authorization = $request->input('Authorization');
        } else {
            $this->throwAuthenticationException($request, 'No Authorization header found');
        }
        
        // Get credentials.
        $authParts = explode(' ', $authorization);
        if (count($authParts) < 2 || $authParts[0] != 'Basic') {
            $this->throwAuthenticationException($request, 'Authorization header is not "Basic"');
        }

        $credentialParts = explode(':', base64_decode(trim($authParts[1])));
        if (count($credentialParts) < 2 || empty($credentialParts[0])) {
            $this->throwAuthenticationException($request, 'Authorization header is not valid');
        }

        // Check credentials.
        if (!$client->checkCredentials([
            'username' => $credentialParts[0],
            'password' => $credentialParts[1],
        ])) {
            $this->throwAuthenticationException($request, 'Credentials are not valid');
        }
    }

    /**
     * Handle a Basic Auth request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $message
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Trax\Framework\Xapi\Exceptions\XapiAuthenticationException
     */
    protected function throwAuthenticationException(Request $request, string $message)
    {
        $exceptionClass = Str::of($request->path())->contains('/xapi/')
            ? XapiAuthenticationException::class
            : AuthenticationException::class;

        throw new $exceptionClass($message);
    }
}
