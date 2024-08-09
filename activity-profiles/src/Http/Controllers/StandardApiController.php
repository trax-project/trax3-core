<?php

namespace Trax\ActivityProfiles\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\ProvideRoutes;
use Trax\Framework\Context;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Framework\Xapi\Http\Controllers\XapiController;
use Trax\Framework\Xapi\Http\Response\DocumentResponse;
use Trax\Framework\Xapi\Exceptions\XapiNotFoundException;
use Trax\ActivityProfiles\ActivityProfilesService;
use Trax\ActivityProfiles\Http\Validation\ValidateStandardApiRequests;
use Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository;
use Trax\ActivityProfiles\Events\ActivityProfilesDeleted;
use Trax\ActivityProfiles\Recording\ActivityProfileRecorder;

class StandardApiController extends XapiController
{
    use ValidateStandardApiRequests, DocumentResponse, ProvideRoutes;

    /**
     * @var string
     */
    protected static $serviceKey = 'activity-profiles';

    /**
     * @var array
     */
    protected static $routes = [
        'stores/{xstore}/xapi/activities/profile' => ['post' => 'post', 'put' => 'put', 'get' => 'get', 'delete' => 'delete'],
    ];

    /**
     * @var \Trax\ActivityProfiles\ActivityProfilesService
     */
    protected $service;

    /**
     * @var \Trax\ActivityProfiles\Recording\ActivityProfileRecorder
     */
    protected $recorder;

    /**
     * @var \Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository
     */
    protected $profiles;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(ActivityProfilesService::class);
        $this->recorder = app(ActivityProfileRecorder::class);
        $this->profiles = app(ActivityProfileRepository::class);
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
        if ($method = $this->checkAlternateRequest($request, 'activities/profile')) {
            return $this->$method($request);
        }

        // Check permissions.
        $this->authorizer->must('activity-profiles/write');

        // Validate request.
        $xapiRequest = $this->validatePostRequest($request);

        // Perform.
        if ($resource = $this->profiles->findFromQuery($xapiRequest->query())) {
            // First, validate content type.
            $this->validateContentType($resource, $xapiRequest->contentType());

            // Record the activity.
            $record = $this->profiles->factory()->update($resource, [
                'content' => $xapiRequest->content(true)
            ], true)->toArray();

            $this->recorder->record([$record]);

            // Logging.
            Logger::xapi(204, $xapiRequest->logData());
        } else {
            // Create the profile.
            $record = [
                'profile_id' => $xapiRequest->param('profileId'),
                'activity_iri' => $xapiRequest->param('activityId'),
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
        $this->authorizer->must('activity-profiles/write');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities/profile');

        // Validate request.
        $xapiRequest = $this->validatePutRequest($request);

        // Search for an existing profile.
        $resource = $this->profiles->findFromQuery($xapiRequest->query());

        // Concurrency.
        $this->validateConcurrency($request, $resource);

        // Perform.
        if ($resource) {
            // Record the activity.
            $record = $this->profiles->factory()->update($resource, [
                'content' => $xapiRequest->content(true),
                'content_type' => $xapiRequest->contentType(),
            ])->toArray();

            $this->recorder->record([$record]);

            // Logging.
            Logger::xapi(204, $xapiRequest->logData());
        } else {
            // Create the profile.
            $record = [
                'activity_iri' => $xapiRequest->param('activityId'),
                'profile_id' => $xapiRequest->param('profileId'),
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
        $this->authorizer->must('activity-profiles/read');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities/profile');

        // Validate request.
        $xapiRequest = $this->validateGetRequest($request);

        if ($xapiRequest->hasParam('profileId')) {
            // Get a single profile.
            if (!$resource = $this->profiles->findFromQuery($xapiRequest->query())) {
                throw new XapiNotFoundException();
            }

            Logger::xapi(200, $xapiRequest->logData());

            return $this->documentResponse($resource->content, $resource->content_type);
        } else {
            // Get multiple profiles.
            $profiles = $this->profiles->get($xapiRequest->query())->pluck('profile_id')->all();

            // Logging.
            Logger::xapi(200, $xapiRequest->logData());

            return $this->documentResponse(
                json_encode($profiles)
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
        $this->authorizer->must('activity-profiles/write');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities/profile');

        // Validate request.
        $xapiRequest = $this->validateDeleteRequest($request);

        // Delete the profiles.
        $this->profiles->deleteByQuery($xapiRequest->query());

        // Logging.
        Logger::xapi(204, $xapiRequest->logData());

        // Events.
        EventManager::dispatch(ActivityProfilesDeleted::class, $xapiRequest->params());

        // Response.
        return response('', 204);
    }
}
