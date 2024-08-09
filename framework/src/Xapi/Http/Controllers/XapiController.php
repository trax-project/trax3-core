<?php

namespace Trax\Framework\Xapi\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Xapi\Exceptions\XapiAuthorizationException;
use Trax\Framework\Auth\Authorizer;

class XapiController extends Controller
{
    /**
     * @var \Trax\Framework\Auth\Authorizer
     */
    protected $authorizer;

    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->authorizer = app(Authorizer::class);
    }

    /**
     * Post a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function post(Request $request)
    {
        throw new XapiAuthorizationException('The POST method is not allowed on this API.');
    }

    /**
     * Put a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function put(Request $request)
    {
        throw new XapiAuthorizationException('The PUT method is not allowed on this API.');
    }

    /**
     * Get a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function get(Request $request)
    {
        throw new XapiAuthorizationException('The GET method is not allowed on this API.');
    }

    /**
     * Delete a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function delete(Request $request)
    {
        throw new XapiAuthorizationException('The DELETE method is not allowed on this API.');
    }

    /**
     * Get a resource with the head method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function head(Request $request)
    {
        return $this->get($request);
    }
}
