<?php

namespace Trax\Agents\Repos\Agent\Actions;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiAgent;

trait OneFunctions
{
    /**
     * Get an existing resource given its params.
     *
     * @param  array|object|string  $json
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getOne($agent)
    {
        return $this->findFromQuery(
            $this->oneQuery($agent)
        );
    }

    /**
     * Delete an existing resource given its params.
     *
     * @param  object|array|string  $agent
     * @return void
     */
    public function deleteOne($agent)
    {
        $this->deleteByQuery(
            $this->oneQuery($agent)
        );
    }

    /**
     * Get the query to target one resource given its params.
     *
     * @param  object|array|string  $agent
     * @return \Trax\Framework\Repo\Query
     */
    protected function oneQuery($agent): Query
    {
        return new Query(['filters' => [
            'id' => XapiAgent::hashId($agent),
        ]]);
    }
}
