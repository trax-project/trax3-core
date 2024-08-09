<?php

namespace Trax\Framework\Xapi\Http\Validation;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Trax\Framework\Xapi\Http\Requests\HttpRequest;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;
use Trax\Framework\Xapi\Exceptions\XapiConflictException;
use Trax\Framework\Xapi\Exceptions\XapiPreconditionFailedException;

trait ValidateConcurrency
{
    /**
     * Validate concurrency.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model|null  $resource
     * @return void
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     * @throws \Trax\Framework\Xapi\Exceptions\XapiConflictException
     * @throws \Trax\Framework\Xapi\Exceptions\XapiPreconditionFailedException
     */
    protected function validateConcurrency(Request $request, $resource)
    {
        // If-Match.
        if (HttpRequest::hasHeader($request, 'If-Match')) {
            if (!$resource) {
                throw new XapiPreconditionFailedException('If-Match header does not match with the existing content.');
            } else {
                // Remove the 'W/' which may be added by some servers or proxies:
                // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Match
                // This should probably be solved by the client.
                $etag = HttpRequest::header($request, 'If-Match');
                if (Str::of($etag)->startsWith('W/')) {
                    $etag = Str::of($etag)->after('W/');
                }

                if ($etag != '"'.sha1($resource->content).'"') {
                    throw new XapiPreconditionFailedException('If-Match header does not match with the existing content.');
                } else {
                    return;
                }
            }
        }
        
        // If-None-Match.
        if (HttpRequest::hasHeader($request, 'If-None-Match')) {
            if (HttpRequest::header($request, 'If-None-Match') != '*') {
                throw new XapiConflictException('Concurrency header If-None-Match must be *.');
            } elseif ($resource) {
                throw new XapiPreconditionFailedException('If-None-Match is set to * but there is an existing content.');
            } else {
                return;
            }
        }
        
        // Missing concurrency data.
        if ($resource) {
            throw new XapiConflictException('Missing concurrency header If-Match or If-None-Match.');
        }
        
        throw new XapiBadRequestException('Missing concurrency header If-Match or If-None-Match.');
    }
}
