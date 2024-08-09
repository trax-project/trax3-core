<?php

namespace Trax\States\Http\Requests;

use Trax\Framework\Http\Requests\FilteringRequest;

class StateApiRequest extends FilteringRequest
{
    /**
     * Filters rules.
     */
    protected $filtersRules = [
        'stateId' => 'string',
        'activityId' => 'iri',
        'agent' => 'xapi_agent',
        'registration' => 'uuid',
        'since' => 'iso_date',
        'type' => 'string',
    ];
}
