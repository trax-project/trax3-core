<?php

namespace Trax\AgentProfiles\Http\Requests;

use Trax\Framework\Http\Requests\FilteringRequest;

class AgentProfileApiRequest extends FilteringRequest
{
    /**
     * Filters rules.
     */
    protected $filtersRules = [
        'profileId' => 'string',
        'agent' => 'xapi_agent',
        'since' => 'iso_date',
        'type' => 'string',
    ];
}
