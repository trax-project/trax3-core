<?php

namespace Trax\ActivityProfiles\Http\Validation;

use Trax\Framework\Xapi\Http\Validation\ValidateDocument;

trait ValidateStandardApiRequests
{
    use ValidateDocument;

    /**
     * PUT and POST rules.
     */
    protected $putRules = [
        'activityId' => 'required|iri',
        'profileId' => 'required|string|forbidden_with:since',
    ];

    /**
     * GET rules.
     */
    protected $getRules = [
        'activityId' => 'required|iri',
        'profileId' => 'string|forbidden_with:since',
        'since' => 'iso_date|forbidden_with:profileId'
    ];

    /**
     * FIND rules.
     */
    protected $deleteRules = [
        'activityId' => 'required|iri',
        'profileId' => 'required|string',
    ];
}
