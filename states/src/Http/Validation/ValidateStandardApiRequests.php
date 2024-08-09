<?php

namespace Trax\States\Http\Validation;

use Trax\Framework\Xapi\Http\Validation\ValidateDocument;

trait ValidateStandardApiRequests
{
    use ValidateDocument;

    /**
     * PUT rules.
     */
    protected $putRules = [
        'activityId' => 'required|iri',
        'agent' => 'required|xapi_agent',
        'registration' => 'uuid',
        'stateId' => 'required|string|forbidden_with:since',
    ];

    /**
     * GET rules.
     */
    protected $getRules = [
        'activityId' => 'required|iri',
        'agent' => 'required|xapi_agent',
        'registration' => 'uuid',
        'stateId' => 'string|forbidden_with:since',
        'since' => 'iso_date|forbidden_with:stateId'
    ];

    /**
     * FIND rules.
     */
    protected $deleteRules = [
        'activityId' => 'required|iri',
        'agent' => 'required|xapi_agent',
        'registration' => 'uuid',
        'stateId' => 'string',
    ];
}
