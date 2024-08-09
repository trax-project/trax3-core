<?php

namespace Trax\AgentProfiles\Repos\AgentProfile\Actions;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiAgent;

trait OneFunctions
{
    /**
     * Get an existing resource given its params.
     *
     * @param  object|array|string  $agent
     * @param  string  $profileId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getOne($agent, string $profileId)
    {
        return $this->findFromQuery(
            $this->oneQuery($agent, $profileId)
        );
    }

    /**
     * Delete an existing resource given its params.
     *
     * @param  object|array|string  $agent
     * @param  string  $profileId
     * @return void
     */
    public function deleteOne($agent, string $profileId)
    {
        $this->deleteByQuery(
            $this->oneQuery($agent, $profileId)
        );
    }

    /**
     * Get the query to target one resource given its params.
     *
     * @param  object|array|string  $agent
     * @param  string  $profileId
     * @return \Trax\Framework\Repo\Query
     */
    protected function oneQuery($agent, string $profileId): Query
    {
        return new Query(['filters' => [
            'agent_id' => XapiAgent::hashId($agent),
            'profile_id' => $profileId,
        ]]);
    }
}
