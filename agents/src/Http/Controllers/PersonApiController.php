<?php

namespace Trax\Agents\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Http\ProvideRoutes;
use Trax\Framework\Auth\Authorizer;
use Trax\Framework\Exceptions\SimpleException;
use Trax\Agents\Recording\PersonRecorder;
use Trax\Agents\Repos\Agent\AgentRepository;

class PersonApiController extends Controller
{
    use ProvideRoutes;

    /**
     * @var string
     */
    protected static $serviceKey = 'agents';

    /**
     * @var array
     */
    protected static $routes = [
       'stores/{xstore}/persons' => ['get' => 'get', 'post' => 'post'],
    ];

    /**
     * @var \Trax\Framework\Auth\Authorizer
     */
    protected $authorizer;
    
    /**
     * @var \Trax\Agents\Repos\Agent\AgentRepository
     */
    protected $agents;
    
    /**
     * @var \Trax\Agents\Recording\PersonRecorder
     */
    protected $recorder;

    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->authorizer = app(Authorizer::class);
        $this->agents = app(AgentRepository::class);
        $this->recorder = app(PersonRecorder::class);
    }

    /**
     * Get a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        // Check permissions.
        $this->authorizer->must('persons/read');

        // Validate the request.
        $inputs = $request->validate([
            'agent' => 'xapi_agent'
        ]);

        // Get the person.
        if (isset($inputs['agent'])) {
            $person = $this->agents->getPersonAgents(
                json_decode($inputs['agent'], true)
            );
        } else {
            $person = [];
        }

        // Logging.
        Logger::http(200);

        // Response.
        return response()->json(['data' => $person]);
    }

    /**
     * Post a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Exceptions\SimpleException
     */
    public function post(Request $request)
    {
        // Check permissions.
        $this->authorizer->must('persons/write');

        // Validate the request and prepare data.
        $persons = json_decode($request->getContent());

        if (is_null($persons)) {
            throw new SimpleException('The body must be a valid JSON array containing persons.');
        }

        $this->recorder->validate($persons);

        // Record the persons.
        $this->recorder->record($persons);

        // Logging.
        Logger::http(204);

        // Response.
        return response('', 204);
    }
}
