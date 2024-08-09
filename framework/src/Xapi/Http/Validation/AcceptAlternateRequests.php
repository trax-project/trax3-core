<?php

namespace Trax\Framework\Xapi\Http\Validation;

use Illuminate\Http\Request;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait AcceptAlternateRequests
{
    /**
     * Supported alternate methods.
     *
     * @var array
     */
    protected $alternateMethods = [
        'statements' => ['GET', 'PUT'],
        'activities/state' => ['GET', 'PUT', 'DELETE'],
        'activities/profile' => ['GET', 'PUT', 'DELETE'],
        'agents/profile' => ['GET', 'PUT', 'DELETE'],
        'activities' => ['GET'],
        'agents' => ['GET'],
        'about' => [],
    ];

    /**
     * Accepted additional inputs for alternate requests.
     *
     * @var array
     */
    protected $alternateInputs = [
        'method',
        'Accept',
        'Accept-Encoding',
        'Accept-Language',
        'Authorization',
        'Content-Type',
        'Content-Length',
        'Content-Transfer-Encoding',
        'X-Experience-API-Version',
        'If-Match',
        'If-None-Match',
    ];

    /**
     * Get the alternate inputs.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return array
     */
    protected function alternateInputs(Request $request)
    {
        return $request->has('method') ? $this->alternateInputs : [];
    }

    /**
     * Check if the request is an alternate request, validate it, and return a redirection method when needed.
     *
     * @param  \Illuminate\Http\Request  $request;
     * @param  string  $api
     * @return  string|false  The redirection method or false.
     */
    protected function checkAlternateRequest(Request $request, string $api)
    {
        // Not an alternate request.
        if (!$request->has('method')) {
            return false;
        }
        // Only POST requests.
        if ($request->method() != 'POST') {
            throw new XapiBadRequestException("Alternate requests must use the POST method.");
        }
            
        // Check that there is only the 'method' param in the query string.
        $query = $request->query();
        if (count($query) > 1 || !isset($query['method'])) {
            throw new XapiBadRequestException("Alternate requests does not support parameters other than 'method'.");
        }

        // Unsupported HTTP method.
        if (!in_array(strtoupper($request->input('method')), $this->alternateMethods[$api])) {
            throw new XapiBadRequestException("The alternate request method is not supported on this API.");
        }

        // Return the method.
        return strtolower($request->input('method'));
    }
}
