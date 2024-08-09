<?php

namespace Trax\Framework\Http\Middleware\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Trax\Framework\Http\Validation\Validator;
use Trax\Framework\Xapi\Http\Requests\HttpRequest;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait HandleXapiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handleXapiVersion(Request $request, $next)
    {
        // xAPI version does not apply.
        if (!Str::of($request->path())->contains('/xapi/')) {
            return $next($request);
        }

        // No version
        if (!HttpRequest::hasHeader($request, 'X-Experience-API-Version')) {
            throw new XapiBadRequestException('Missing X-Experience-API-Version header.');
        }

        // Wrong format
        $version = HttpRequest::header($request, 'X-Experience-API-Version');
        if (!Validator::check($version, 'xapi_version')) {
            throw new XapiBadRequestException("Incorrect X-Experience-API-Version header: [$version].");
        }
        
        // Fine, we continue
        $response =  $next($request);
        
        // Add xAPI header to responses.
        $response->header('X-Experience-API-Version', '1.0.3');
        
        return $response;
    }
}
