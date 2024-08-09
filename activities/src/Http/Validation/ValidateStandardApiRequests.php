<?php

namespace Trax\Activities\Http\Validation;

use Illuminate\Http\Request;
use Trax\Framework\Xapi\Http\Requests\XapiRequest;
use Trax\Framework\Xapi\Http\Validation\ValidateRules;
use Trax\Framework\Xapi\Http\Validation\PreventUnkownInputs;
use Trax\Framework\Xapi\Http\Validation\AcceptAlternateRequests;

trait ValidateStandardApiRequests
{
    use ValidateRules, PreventUnkownInputs, AcceptAlternateRequests;

    /**
     * GET rules.
     */
    protected $getRules = [
        'activityId' => 'required|iri',
    ];

    /**
     * Validate a GET request.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return \Trax\Framework\Xapi\Http\Requests\XapiRequest
     */
    protected function validateGetRequest(Request $request): XapiRequest
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
}
