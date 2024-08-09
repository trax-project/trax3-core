<?php

namespace Trax\Statements\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Http\ProvideRoutes;
use Trax\Framework\Context;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Xapi\Helpers\XapiAttachment;
use Trax\Framework\Xapi\Http\Controllers\XapiController;
use Trax\Framework\Xapi\Exceptions\XapiNotFoundException;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;
use Trax\Statements\Http\Validation\ValidateStandardApiRequests;
use Trax\Statements\Repos\Statement\StatementRepository;
use Trax\Statements\Repos\Attachment\AttachmentRepository;
use Trax\Statements\Recording\StatementRecorder;
use Trax\Statements\Http\Controllers\Actions\BuildResponse;

class StandardApiController extends XapiController
{
    use ValidateStandardApiRequests, BuildResponse, ProvideRoutes;

    /**
     * @var string
     */
    protected static $serviceKey = 'statements';

    /**
     * @var array
     */
    protected static $routes = [
        'stores/{xstore}/xapi/statements' => ['post' => 'post', 'put' => 'put', 'get' => 'get', 'delete' => 'delete'],
    ];

    /**
     * @var \Trax\Statements\Repos\Statement\StatementRepository
     */
    protected $statements;
    
    /**
     * @var \Trax\Statements\Repos\Attachment\AttachmentRepository
     */
    protected $attachments;
    
    /**
     * @var \Trax\Statements\Recording\StatementRecorder
     */
    protected $recorder;

    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->statements = app(StatementRepository::class);
        $this->attachments = app(AttachmentRepository::class);
        $this->recorder = app(StatementRecorder::class);
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
        if ($method = $this->checkAlternateRequest($request, 'statements')) {
            return $this->$method($request);
        }

        // Check permissions.
        $this->authorizer->must('statements/write');

        // Validate the request.
        $xapiRequest = $this->validatePostRequest($request);
        
        // Record the statements.
        $uuids = $this->recorder->record($xapiRequest->statements(), $xapiRequest->attachments());

        // Logging.
        Logger::xapi(200, $xapiRequest->logData());
        
        // Response.
        return response()->json($uuids);
    }

    /**
     * Put a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function put(Request $request)
    {
        // Override the context method because of alternate requests.
        Context::setMethod('put');

        // Check permissions.
        $this->authorizer->must('statements/write');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'statements');

        // Validate the request.
        $xapiRequest = $this->validatePutRequest($request);

        // Record the statements.
        $this->recorder->record($xapiRequest->statements(), $xapiRequest->attachments());

        // Logging.
        Logger::xapi(204, $xapiRequest->logData());

        // Response.
        return response('', 204);
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
        $this->authorizer->must('statements/read');

        // Check alternate request.
        $this->checkAlternateRequest($request, 'statements');

        // Find a given statement.
        if ($request->has('statementId') || $request->has('voidedStatementId')) {
            return $this->find($request);
        }

        // Validate the request.
        try {
            $xapiRequest = $this->validateGetRequest($request);
        } catch (XapiBadRequestException $e) {
            $e->addHeaders(
                ['X-Experience-API-Consistent-Through' => $this->statements->consistentThrough()]
            );
            throw $e;
        }

        // Perform request.
        $resources = $this->statements->xapiQuery($xapiRequest->query());

        // Prepare response.
        $content = ['statements' => $resources->pluck('raw')->all()];
        if ($more = $this->moreUrl($request, $xapiRequest, $resources)) {
            $content['more'] = $more;
        }

        // Logging.
        Logger::xapi(200, $xapiRequest->logData());

        // Response.
        if ($xapiRequest->param('attachments') == 'true') {
            $attachments = $this->attachments->in(
                XapiAttachment::sha2sFromStatements($resources)
            );
            $response = $this->multipartResponse($content, $attachments);
        } else {
            $response = response()->json($content);
        }
        return $response->header('X-Experience-API-Consistent-Through', $this->statements->consistentThrough());
    }

    /**
     * Get a given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     * @throws \Trax\Framework\Xapi\Exceptions\XapiNotFoundException
     */
    protected function find(Request $request)
    {
        // Validate request.
        try {
            $xapiRequest = $this->validateFindRequest($request);
        } catch (XapiBadRequestException $e) {
            $e->addHeaders(
                ['X-Experience-API-Consistent-Through' => $this->statements->consistentThrough()]
            );
            throw $e;
        }

        // Perform request.
        if (!$resource = $this->statements->get($xapiRequest->query())->first()) {
            throw (new XapiNotFoundException())->addHeaders(
                ['X-Experience-API-Consistent-Through' => $this->statements->consistentThrough()]
            );
        }

        // Logging.
        Logger::xapi(200, $xapiRequest->logData());

        // Response.
        if ($xapiRequest->param('attachments') == 'true') {
            $attachments = $this->attachments->in(
                XapiAttachment::sha2sFromStatements(collect([$resource]))
            );
            $response = $this->multipartResponse($resource->raw, $attachments);
        } else {
            $response = response()->json($resource->raw);
        }
        return $response->header('X-Experience-API-Consistent-Through', $this->statements->consistentThrough());
    }
}
