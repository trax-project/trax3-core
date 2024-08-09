<?php

namespace Trax\Statements\Http\Requests;

use Trax\Framework\Http\Requests\FilteringRequest;

class StatementApiRequest extends FilteringRequest
{
    /**
     * Filters rules.
     */
    protected $filtersRules = [
        'agent' => 'xapi_agent',
        'verb' => 'iri',
        'activity' => 'iri',
        'since' => 'iso_date',
        'until' => 'iso_date',
        'type' => 'iri',
        'profile' => 'iri',
        'pseudonymized' => 'mixed_boolean',
        'voided' => 'mixed_boolean',
    ];
    
    /**
     * Options rules.
     */
    protected $optionsRules = [
        'rearrange' => 'mixed_boolean',
        'agent_location' => 'string|in:actor,object,everywhere',
        'activity_location' => 'string|in:object,everywhere',
    ];
}
