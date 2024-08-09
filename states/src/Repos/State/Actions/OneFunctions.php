<?php

namespace Trax\States\Repos\State\Actions;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\XapiActivity;

trait OneFunctions
{
    /**
     * Get an existing resource given its params.
     *
     * @param  string  $activityId
     * @param  object|array|string  $agent
     * @param  string  $stateId
     * @param  string  $registration
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getOne(string $activityId, $agent, string $stateId, string $registration = null)
    {
        return $this->findFromQuery(
            $this->oneQuery($activityId, $agent, $stateId, $registration)
        );
    }

    /**
     * Delete an existing resource given its params.
     *
     * @param  string  $activityId
     * @param  object|array|string  $agent
     * @param  string  $stateId
     * @param  string  $registration
     * @return void
     */
    public function deleteOne(string $activityId, $agent, string $stateId, string $registration = null)
    {
        $this->deleteByQuery(
            $this->oneQuery($activityId, $agent, $stateId, $registration)
        );
    }

    /**
     * Get the query to target one resource given its params.
     *
     * @param  string  $activityId
     * @param  object|array|string  $agent
     * @param  string  $stateId
     * @param  string  $registration
     * @return \Trax\Framework\Repo\Query
     */
    protected function oneQuery(string $activityId, $agent, string $stateId, string $registration = null): Query
    {
        return new Query(['filters' => [
            'activity_id' => XapiActivity::hashId($activityId),
            'agent_id' => XapiAgent::hashId($agent),
            'state_id' => $stateId,
            'registration' => $registration,
        ]]);
    }
}
