<?php

namespace Trax\States\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\ProvideRoutes;
use Trax\Framework\Context;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Framework\Xapi\Http\Controllers\XapiController;
use Trax\Framework\Xapi\Http\Response\DocumentResponse;
use Trax\Framework\Xapi\Exceptions\XapiNotFoundException;
use Trax\States\StatesService;
use Trax\States\Http\Validation\ValidateStandardApiRequests;
use Trax\States\Repos\State\StateRepository;
use Trax\States\Events\StatesDeleted;
use Trax\States\Recording\StateRecorder;

class StandardApiController extends XapiController
{
    use ValidateStandardApiRequests, DocumentResponse, ProvideRoutes;

    /**
     * @var string
     */
    protected static $serviceKey = 'states';

    /**
     * @var array
     */
    protected static $routes = [
        'stores/{xstore}/xapi/activities/state' => ['post' => 'post', 'put' => 'put', 'get' => 'get', 'delete' => 'delete'],
    ];

    /**
     * @var \Trax\States\StatesService
     */
    protected $service;

    /**
     * @var \Trax\States\Recording\StateRecorder
     */
    protected $recorder;

    /**
     * @var \Trax\States\Repos\State\StateRepository
     */
    protected $states;
    
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(StatesService::class);
        $this->recorder = app(StateRecorder::class);
        $this->states = app(StateRepository::class);
    }
    
    /**
     * Post a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function post(Request $request)
    {
        // Check alternate request.
        if ($method = $this->checkAlternateRequest($request, 'activities/state')) {
            return $this->$method($request);
        }

        // Check permissions.
        $this->authorizer->must('states/write');

        // Validate request.
        $xapiRequest = $this->validatePostRequest($request);

        // Perform.
        if ($resource = $this->states->findFromQuery($xapiRequest->query())) {
            // First, validate content type.
            $this->validateContentType($resource, $xapiRequest->contentType());

            // Record the state.
            $record = $this->states->factory()->update($resource, [
                'content' => $xapiRequest->content(true)
            ], true)->toArray();

            $this->recorder->record([$record]);

            // Logging.
            Logger::xapi(204, $xapiRequest->logData());
        } else {
            // Record the state.
            $record = [
                'state_id' => $xapiRequest->param('stateId'),
                'activity_iri' => $xapiRequest->param('activityId'),
                'agent' => $xapiRequest->param('agent'),
                'registration' => $xapiRequest->param('registration'),
                'content' => $xapiRequest->content(true),
                'content_type' => $xapiRequest->contentType(),
            ];
            $this->recorder->record([$record]);

            // Logging.
            Logger::xapi(204, $xapiRequest->logData());
        }

        // Response.
        return response('', 204);
    }

    /**
     * Post a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function put(Request $request)
    {
        // Override the context method because of alternate requests.
        Context::setMethod('put');

        // Check permissions.
        $this->authorizer->must('states/write');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities/state');

        // Validate request.
        $xapiRequest = $this->validatePutRequest($request);

        // Perform.
        if ($resource = $this->states->findFromQuery($xapiRequest->query())) {
            // Record the state.
            $record = $this->states->factory()->update($resource, [
                'content' => $xapiRequest->content(true),
                'content_type' => $xapiRequest->contentType(),
            ])->toArray();

            $this->recorder->record([$record]);

            // Logging.
            Logger::xapi(204, $xapiRequest->logData());
        } else {
            // Record the state.
            $record = [
                'state_id' => $xapiRequest->param('stateId'),
                'activity_iri' => $xapiRequest->param('activityId'),
                'agent' => $xapiRequest->param('agent'),
                'registration' => $xapiRequest->param('registration'),
                'content' => $xapiRequest->content(true),
                'content_type' => $xapiRequest->contentType(),
            ];
            $this->recorder->record([$record]);

            // Logging.
            Logger::xapi(204, $xapiRequest->logData());
        }

        // Response.
        return response('', 204);
    }

    /**
     * Get resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiNotFoundException
     */
    public function get(Request $request)
    {
        // Override the context method because of alternate requests.
        Context::setMethod('get');

        // Check permissions.
        $this->authorizer->must('states/read');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities/state');

        // Validate request.
        $xapiRequest = $this->validateGetRequest($request);

        if ($xapiRequest->hasParam('stateId')) {
            // Get a single state.
            if (!$resource = $this->states->findFromQuery($xapiRequest->query())) {
                throw new XapiNotFoundException();
            }

            Logger::xapi(200, $xapiRequest->logData());

            return $this->documentResponse($resource->content, $resource->content_type);
        } else {
            // Get multiple states.
            $states = $this->states->get($xapiRequest->query())->pluck('state_id')->all();

            // Logging.
            Logger::xapi(200, $xapiRequest->logData());

            return $this->documentResponse(
                json_encode($states)
            );
        }
    }

    /**
     * Delete a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Override the context method because of alternate requests.
        Context::setMethod('delete');

        // Check permissions.
        $this->authorizer->must('states/write');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities/state');

        // Validate request.
        $xapiRequest = $this->validateDeleteRequest($request);

        // Delete states.
        $this->states->deleteByQuery($xapiRequest->query());

        // Logging.
        Logger::xapi(204, $xapiRequest->logData());

        // Events.
        EventManager::dispatch(StatesDeleted::class, $xapiRequest->params());

        // Response.
        return response('', 204);
    }
}
