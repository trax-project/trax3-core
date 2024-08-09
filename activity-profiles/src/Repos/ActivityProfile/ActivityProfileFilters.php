<?php

namespace Trax\ActivityProfiles\Repos\ActivityProfile;

use Trax\Framework\Xapi\Helpers\HasDocumentFilters;

trait ActivityProfileFilters
{
    use HasDocumentFilters;

    /**
     * Get the dynamic filters.
     *
     * @return array
     */
    public function dynamicFilters(): array
    {
        return [
            // Standard filters.
            'activityId',
            'profileId',
            'since',

            // Extended filters.
            'type',
        ];
    }
}
