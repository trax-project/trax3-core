<?php

namespace Trax\Framework\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Auth\Authorizer;
use Trax\Framework\Http\Requests\FilteringRequest;

trait ImplementRetrievalApiMethods
{
    // TO BE DEFINED
    // protected static $serviceKey = 'statements';

    // TO BE DEFINED
    /*
    protected static $api = [
        'key' => 'activities',
        'domain' => 'activities',
        'request' => \Trax\Activities\Http\Requests\ActivityApiRequest::class,
    ];
    */

    /**
     * @var \Trax\Framework\Auth\Authorizer
     */
    protected $authorizer;
    
    /**
     * @var \Trax\Framework\Repo\RepositoryInterface
     */
    protected $repo;

    /**
     * @return void
     */
    public function constructRetrievalApi()
    {
        $this->authorizer = app(Authorizer::class);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    public function get(Request $request)
    {
        // Check permissions.
        $this->authorizer->must(self::$api['domain'] . '/read');
        if (isset(self::$api['subdomain'])) {
            $this->authorizer->must(self::$api['subdomain']);
        }

        // Validate the request.
        $requestClass = isset(self::$api['request']) ? self::$api['request'] : FilteringRequest::class;
        $crudRequest = (new $requestClass)->validate($request);

        // Perform request.
        $resources = $this->repo->get($crudRequest->query());

        // Logging.
        Logger::http(200);

        // Response.
        return response()->json([
            'data' => $resources
        ]);
    }
}
