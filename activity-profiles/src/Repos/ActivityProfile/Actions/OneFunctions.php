<?php

namespace Trax\ActivityProfiles\Repos\ActivityProfile\Actions;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiActivity;

trait OneFunctions
{
    /**
     * Get an existing resource given its params.
     *
     * @param  string  $activityId
     * @param  string  $profileId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getOne(string $activityId, string $profileId)
    {
        return $this->findFromQuery(
            $this->oneQuery($activityId, $profileId)
        );
    }

    /**
     * Delete an existing resource given its params.
     *
     * @param  string  $activityId
     * @param  string  $profileId
     * @return void
     */
    public function deleteOne(string $activityId, string $profileId)
    {
        $this->deleteByQuery(
            $this->oneQuery($activityId, $profileId)
        );
    }

    /**
     * Get the query to target one resource given its params.
     *
     * @param  string  $activityId
     * @param  string  $profileId
     * @return \Trax\Framework\Repo\Query
     */
    protected function oneQuery(string $activityId, string $profileId): Query
    {
        return new Query(['filters' => [
            'activity_id' => XapiActivity::hashId($activityId),
            'profile_id' => $profileId,
        ]]);
    }
}
