<?php

namespace Trax\AgentProfiles\Repos\AgentProfile;

use Trax\Framework\Xapi\Helpers\HasDocumentFilters;

trait AgentProfileFilters
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
            'agent',
            'profileId',
            'since',

            // Extended filters.
            'type',
            'person',
        ];
    }
}
