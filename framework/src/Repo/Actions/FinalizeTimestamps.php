<?php

namespace Trax\Framework\Repo\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Xapi\Helpers\XapiDate;

trait FinalizeTimestamps
{
    /**
     * Finalize the timestamps of a collection of resources.
     *
     * @param  \Illuminate\Support\Collection  $resources
     * @return \Illuminate\Support\Collection
     */
    public function finalizeTimestamps(Collection $resources): Collection
    {
        // Change the stored column which may have a different format with PostgreSQL.
        return $resources->map(function ($resource) {
            if (isset($resource->stored)) {
                $resource->stored = XapiDate::normalize($resource->stored);
            }
            if (isset($resource->updated)) {
                $resource->updated = XapiDate::normalize($resource->updated);
            }
            return $resource;
        });
    }
}
