<?php

namespace Trax\States\Repos\State;

use Trax\Framework\Xapi\Helpers\HasDocumentFilters;

trait StateFilters
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
            'agent',
            'stateId',
            'since',

            // Extended filters.
            'type',
            'person',
        ];
    }
}
