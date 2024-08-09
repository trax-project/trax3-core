<?php

namespace Trax\ActivityProfiles\Http\Requests;

use Trax\Framework\Http\Requests\FilteringRequest;

class ActivityProfileApiRequest extends FilteringRequest
{
    /**
     * Filters rules.
     */
    protected $filtersRules = [
        'profileId' => 'string',
        'activityId' => 'iri',
        'since' => 'iso_date',
        'type' => 'string',
    ];
}
