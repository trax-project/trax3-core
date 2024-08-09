<?php

namespace Trax\AgentProfiles\Http\Controllers;

use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\Controllers\DeclareRetrievalApiRoutes;
use Trax\Framework\Http\Controllers\ImplementRetrievalApiMethods;
use Trax\AgentProfiles\Http\Requests\AgentProfileApiRequest;
use Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository;

class AgentProfileApiController extends Controller
{
    use DeclareRetrievalApiRoutes, ImplementRetrievalApiMethods;

    /**
     * @var string
     */
    protected static $serviceKey = 'agent-profiles';

    /**
     * @var array
     */
    protected static $api = [
        'key' => 'agent-profiles',
        'domain' => 'agent-profiles',
        'subdomain' => 'exploration',
        'request' => AgentProfileApiRequest::class,
    ];
    
    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->constructRetrievalApi();
        $this->repo = app(AgentProfileRepository::class);
    }
}
