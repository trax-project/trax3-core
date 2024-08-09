<?php

namespace Trax\Framework\Xapi\Http\Validation;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Trax\Framework\Xapi\Http\Requests\XapiRequest;
use Trax\Framework\Xapi\Http\Requests\HttpRequest;
use Trax\Framework\Xapi\Http\Validation\ValidateRules;
use Trax\Framework\Xapi\Http\Validation\PreventUnkownInputs;
use Trax\Framework\Xapi\Http\Validation\AcceptAlternateRequests;
use Trax\Framework\Xapi\Http\Validation\ValidateConcurrency;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

/**
 * The $putRules, $getRules and $deleteRules properties must be defined in the using class.
 */
trait ValidateDocument
{
    use ValidateConcurrency, ValidateRules, PreventUnkownInputs, AcceptAlternateRequests;

    /**
     * Validate a POST request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Framework\Xapi\Http\Requests\XapiRequest
     */
    protected function validatePostRequest(Request $request): XapiRequest
    {
        return $this->validatePutRequest($request);
    }

    /**
     * Validate a PUT request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Framework\Xapi\Http\Requests\XapiRequest
     */
    protected function validatePutRequest(Request $request): XapiRequest
    {
        // Validate rules.
        $this->validateRules($request, $this->putRules);
        
        // Validate content.
        list($content, $type) = $this->validateContent($request);

        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, array_merge(
            ['content'],
            array_keys($this->putRules),
            $this->alternateInputs($request)
        ));

        // Return the request.
        return new XapiRequest($params, $content, $type);
    }

    /**
     * Validate a GET request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Framework\Xapi\Http\Requests\XapiRequest
     */
    protected function validateGetRequest(Request $request)
    {
        // Validate rules.
        $this->validateRules($request, $this->getRules);
        
        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, array_merge(
            array_keys($this->getRules),
            $this->alternateInputs($request)
        ));

        // Return the request.
        return new XapiRequest($params);
    }

    /**
     * Validate a DELETE request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Framework\Xapi\Http\Requests\XapiRequest
     */
    protected function validateDeleteRequest(Request $request)
    {
        // Validate rules.
        $this->validateRules($request, $this->deleteRules);
        
        // Prevent unknown inputs.
        $params = $this->preventUnkownInputs($request, array_merge(
            array_keys($this->deleteRules),
            $this->alternateInputs($request)
        ));

        // Return the request.
        return new XapiRequest($params);
    }

    /**
     * Validate request content.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function validateContent(Request $request): array
    {
        // JSON content type.
        if ($request->isJson()) {
            if (!$document = json_decode(HttpRequest::content($request), true)) {
                throw new XapiBadRequestException('Invalid JSON content.');
            }
            return [$document, 'application/json'];
        }
        
        // Other content type.
        if (HttpRequest::hasHeader($request, 'Content-Type')) {
            return [
                HttpRequest::content($request),
                HttpRequest::header($request, 'Content-Type')
            ];
        }
        
        // Unknown content type.
        throw new XapiBadRequestException('Missing Content-Type header.');
    }

    /**
     * Validate request content.
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param string  $contentType
     * @return void
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function validateContentType(Model $model, string $contentType): void
    {
        $modelIsJson = strpos('application/json', $model->content_type) !== false;
        $dataIsJson = strpos('application/json', $contentType) !== false;

        if (($modelIsJson || $dataIsJson) && $modelIsJson != $dataIsJson) {
            throw new XapiBadRequestException('JSON content can not be merged because one content type is not JSON.');
        }
    }
}
