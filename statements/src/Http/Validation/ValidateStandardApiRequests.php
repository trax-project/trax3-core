<?php

namespace Trax\Statements\Http\Validation;

use Illuminate\Http\Request;
use Trax\Framework\Xapi\Http\Requests\HttpRequest;
use Trax\Framework\Xapi\Http\Requests\XapiStatementRequest;
use Trax\Framework\Xapi\Http\Validation\ValidateRules;
use Trax\Framework\Xapi\Http\Validation\PreventUnkownInputs;
use Trax\Framework\Xapi\Http\Validation\AcceptAlternateRequests;

trait ValidateStandardApiRequests
{
    use ValidateStatementContent, ValidateRules, PreventUnkownInputs, AcceptAlternateRequests;

    /**
     * @var array
     */
    protected $putRules = [
        'statementId' => 'required|uuid',
    ];

    /**
     * GET rules.
     */
    protected $getRules = [
        'agent' => 'xapi_agent',
        'verb' => 'iri',
        'activity' => 'iri',
        'registration' => 'uuid',
        'related_activities' => 'json_boolean',
        'related_agents' => 'json_boolean',
        'since' => 'iso_date',
        'until' => 'iso_date',
        'limit' => 'integer|min:0',
        'format' => 'xapi_format',
        'attachments' => 'json_boolean',
        'ascending' => 'json_boolean',
    ];

    /**
     * FIND rules.
     */
    protected $findRules = [
        'statementId' => 'uuid|forbidden_with:voidedStatementId',
        'voidedStatementId' => 'uuid|forbidden_with:statementId',
        'format' => 'xapi_format',
        'attachments' => 'json_boolean',
    ];

    /**
     * Validate a POST request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Trax\Framework\Xapi\Http\Requests\XapiStatementRequest
     */
    protected function validatePostRequest(Request $request): XapiStatementRequest
    {
        // Validate content.
        list($statements, $attachments) = $this->validatePostRequestContent($request);

        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, []);

        // Return the request.
        return new XapiStatementRequest($params, $statements, $attachments);
    }

    /**
     * Validate a PUT request.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Trax\Framework\Xapi\Http\Requests\XapiStatementRequest
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiConflictException
     */
    protected function validatePutRequest(Request $request): XapiStatementRequest
    {
        // Validate rules.
        $this->validateRules($request, $this->putRules);

        // Validate content.
        list($statement, $attachments) = $this->validatePutRequestContent($request);
        
        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, array_merge(
            ['content'],
            array_keys($this->putRules),
            $this->alternateInputs($request)
        ));

        // Set the statement ID. Potential conflict will be checked later by the recorder.
        $statement->id = $request->input('statementId');

        // Return the request.
        return new XapiStatementRequest($params, $statement, $attachments);
    }

    /**
     * Validate a GET request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Framework\Xapi\Http\Requests\XapiStatementRequest
     */
    protected function validateGetRequest(Request $request): XapiStatementRequest
    {
        // Validate rules.
        $this->validateRules($request, $this->getRules);
                
        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, array_merge(
            ['after', 'before'],
            array_keys($this->getRules),
            $this->alternateInputs($request)
        ));

        // Don't forget the lang.
        $params['lang'] = HttpRequest::header($request, 'Accept-Language', 'en');

        // Return the request.
        return new XapiStatementRequest($params);
    }

    /**
     * Validate a FIND request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Statements\Repos\Statement\XapiStatementRequest
     *
     * @throws \Trax\XapiStore\Exceptions\XapiBadRequestException
     */
    protected function validateFindRequest(Request $request): XapiStatementRequest
    {
        // Validate rules.
        $this->validateRules($request, $this->findRules);

        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, array_merge(
            array_keys($this->findRules),
            $this->alternateInputs($request)
        ));

        // Don't forget the lang.
        $params['lang'] = HttpRequest::header($request, 'Accept-Language', 'en');

        // Return the request.
        return new XapiStatementRequest($params);
    }
}
