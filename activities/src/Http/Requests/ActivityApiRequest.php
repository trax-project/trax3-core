<?php

namespace Trax\Activities\Http\Requests;

use Trax\Framework\Http\Requests\FilteringRequest;

class ActivityApiRequest extends FilteringRequest
{
    /**
     * Filters rules.
     */
    protected $filtersRules = [
        'activityId' => 'iri',
        'type' => 'iri',
        'is_category' => 'mixed_boolean',
        'is_profile' => 'mixed_boolean',
    ];
    
    /**
     * Options rules.
     */
    protected $optionsRules = [
        'rearrange' => 'mixed_boolean',
    ];
}
