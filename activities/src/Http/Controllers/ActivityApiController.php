<?php

namespace Trax\Activities\Http\Controllers;

use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\Controllers\DeclareRetrievalApiRoutes;
use Trax\Framework\Http\Controllers\ImplementRetrievalApiMethods;
use Trax\Activities\Http\Requests\ActivityApiRequest;
use Trax\Activities\Repos\Activity\ActivityRepository;

class ActivityApiController extends Controller
{
    use DeclareRetrievalApiRoutes, ImplementRetrievalApiMethods;

    /**
     * @var string
     */
    protected static $serviceKey = 'activities';

    /**
     * @var array
     */
    protected static $api = [
        'key' => 'activities',
        'domain' => 'activities',
        'subdomain' => 'exploration',
        'request' => ActivityApiRequest::class,
    ];
    
    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->constructRetrievalApi();
        $this->repo = app(ActivityRepository::class);
    }
}
