<?php

namespace Trax\Activities\Repos\Activity\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Schema\Statement as StatementSchema;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Repo\Actions\FinalizeTimestamps;

trait FinalizeActivities
{
    use FinalizeTimestamps;

    /**
     * Finalize the resources before returning them.
     *
     * @param  \Illuminate\Support\Collection  $resources
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function finalize(Collection $resources, Query $query = null): Collection
    {
        // Change the timesamps which may have a different format with PostgreSQL.
        $resources = $this->finalizeTimestamps($resources);

        // Nothing to do;
        if (!isset($query)) {
            return $resources;
        }
        if (!$query->option('rearrange', false)) {
            return $resources;
        }

        return $resources->map(function ($resource) {
            $resource->definition = StatementSchema::reorderDefinition(
                Json::object($resource->definition)
            );
            return $resource;
        });
    }
}
