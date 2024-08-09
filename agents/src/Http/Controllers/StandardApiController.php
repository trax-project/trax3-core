<?php

namespace Trax\Agents\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\ProvideRoutes;
use Trax\Framework\Context;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Xapi\Http\Controllers\XapiController;
use Trax\Agents\AgentsService;
use Trax\Agents\Http\Validation\ValidateStandardApiRequests;
use Trax\Agents\Repos\Agent\AgentRepository;

class StandardApiController extends XapiController
{
    use ValidateStandardApiRequests, ProvideRoutes;

    /**
     * @var string
     */
    protected static $serviceKey = 'agents';

    /**
     * @var array
     */
    protected static $routes = [
        'stores/{xstore}/xapi/agents' => ['post' => 'post', 'put' => 'put', 'get' => 'get', 'delete' => 'delete'],
    ];

    /**
     * @var \Trax\Agents\AgentsService
     */
    protected $service;

    /**
     * @var \Trax\Agents\Repos\Agent\AgentRepository
     */
    protected $agents;
    
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(AgentsService::class);
        $this->agents = app(AgentRepository::class);
    }

    /**
     * Post a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function post(Request $request)
    {
        // Check alternate request.
        if ($method = $this->checkAlternateRequest($request, 'agents')) {
            return $this->$method($request);
        }

        return parent::post($request);
    }

    /**
     * Get a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    public function get(Request $request)
    {
        // Override the context method because of alternate requests.
        Context::setMethod('get');

        // Check permissions.
        $this->authorizer->must('agents/read');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'agents');

        // Validate the request.
        $xapiRequest = $this->validateGetRequest($request);
        
        // Get the person.
        $person = $this->agents->getXapiPerson(
            json_decode($xapiRequest->param('agent'), true)
        );

        // Logging.
        Logger::xapi(200, $xapiRequest->logData());

        // Response.
        return response()->json($person);
    }
}
