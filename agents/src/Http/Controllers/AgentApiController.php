<?php

namespace Trax\Agents\Http\Controllers;

use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\Controllers\DeclareRetrievalApiRoutes;
use Trax\Framework\Http\Controllers\ImplementRetrievalApiMethods;
use Trax\Agents\Http\Requests\AgentApiRequest;
use Trax\Agents\Repos\Agent\AgentRepository;

class AgentApiController extends Controller
{
    use DeclareRetrievalApiRoutes, ImplementRetrievalApiMethods;

    /**
     * @var string
     */
    protected static $serviceKey = 'agents';

    /**
     * @var array
     */
    protected static $api = [
        'key' => 'agents',
        'domain' => 'agents',
        'subdomain' => 'exploration',
        'request' => AgentApiRequest::class,
    ];
    
    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->constructRetrievalApi();
        $this->repo = app(AgentRepository::class);
    }
}
