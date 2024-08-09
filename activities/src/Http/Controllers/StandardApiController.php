<?php

namespace Trax\Activities\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\ProvideRoutes;
use Trax\Framework\Context;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Xapi\Http\Controllers\XapiController;
use Trax\Activities\ActivitiesService;
use Trax\Activities\Http\Validation\ValidateStandardApiRequests;
use Trax\Activities\Repos\Activity\ActivityRepository;

class StandardApiController extends XapiController
{
    use ValidateStandardApiRequests, ProvideRoutes;

    /**
     * @var string
     */
    protected static $serviceKey = 'activities';

    /**
     * @var array
     */
    protected static $routes = [
        'stores/{xstore}/xapi/activities' => ['post' => 'post', 'put' => 'put', 'get' => 'get', 'delete' => 'delete'],
    ];

    /**
     * @var \Trax\Activities\ActivitiesService
     */
    protected $service;

    /**
     * @var \Trax\Activities\Repos\Activity\ActivityRepository
     */
    protected $activities;
    
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = app(ActivitiesService::class);
        $this->activities = app(ActivityRepository::class);
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
        if ($method = $this->checkAlternateRequest($request, 'activities')) {
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
        $this->authorizer->must('activities/read');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'activities');

        // Validate the request.
        $xapiRequest = $this->validateGetRequest($request);

        // Default response content.
        $content = (object)[
            'objectType' => 'Activity',
            'id' => $xapiRequest->param('activityId')
        ];

        // Check permissions / Perform request.
        $resource = $this->activities->getOne($xapiRequest->param('activityId'));

        // Search for an existing definition.
        if ($resource && !empty($resource->definition)) {
            $content->definition = $resource->definition;
        }

        // Logging.
        Logger::xapi(200, $xapiRequest->logData());

        // Response.
        return response()->json($content);
    }
}
