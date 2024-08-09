<?php

namespace Trax\Agents\Repos\Agent\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;
use Trax\Framework\Repo\Actions\FinalizeTimestamps;
use Trax\Framework\Xapi\Helpers\XapiAgent;

trait FinalizeAgents
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
        if (!$query->option('join_members', false)) {
            return $resources;
        }

        // Get all the member references.
        $memberIds = $resources->reduce(function ($members, $resource) {
            if ($resource->is_group && $resource->members_count > 0) {
                return $members->concat($resource->members);
            }
            return $members;
        }, collect([]))->unique();

        // Get all the member agents.
        $members = $this->in($memberIds)->keyBy('id')->map(function ($member) {
            return XapiAgent::jsonByModel($member, true);
        });

        // Replace member references by member agents.
        return $resources->map(function ($resource) use ($members) {
            if ($resource->is_group && $resource->members_count > 0) {
                $resource->members = $members->only($resource->members)->values();
            }
            return $resource;
        });
    }
}
