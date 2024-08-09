<?php

namespace Trax\AgentProfiles\Http\Validation;

use Trax\Framework\Xapi\Http\Validation\ValidateDocument;

trait ValidateStandardApiRequests
{
    use ValidateDocument;

    /**
     * PUT and POST rules.
     */
    protected $putRules = [
        'agent' => 'required|xapi_agent',
        'profileId' => 'required|string|forbidden_with:since',
    ];

    /**
     * GET rules.
     */
    protected $getRules = [
        'agent' => 'required|xapi_agent',
        'profileId' => 'string|forbidden_with:since',
        'since' => 'iso_date|forbidden_with:profileId'
    ];

    /**
     * FIND rules.
     */
    protected $deleteRules = [
        'agent' => 'required|xapi_agent',
        'profileId' => 'required|string',
    ];
}
