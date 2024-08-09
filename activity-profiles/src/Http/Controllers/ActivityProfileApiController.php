<?php

namespace Trax\ActivityProfiles\Http\Controllers;

use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\Controllers\DeclareRetrievalApiRoutes;
use Trax\Framework\Http\Controllers\ImplementRetrievalApiMethods;
use Trax\ActivityProfiles\Http\Requests\ActivityProfileApiRequest;
use Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository;

class ActivityProfileApiController extends Controller
{
    use DeclareRetrievalApiRoutes, ImplementRetrievalApiMethods;

    /**
     * @var string
     */
    protected static $serviceKey = 'activity-profiles';

    /**
     * @var array
     */
    protected static $api = [
        'key' => 'activity-profiles',
        'domain' => 'activity-profiles',
        'subdomain' => 'exploration',
        'request' => ActivityProfileApiRequest::class,
    ];
    
    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->constructRetrievalApi();
        $this->repo = app(ActivityProfileRepository::class);
    }
}
