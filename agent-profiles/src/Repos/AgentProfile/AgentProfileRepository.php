<?php

namespace Trax\AgentProfiles\Repos\AgentProfile;

use Trax\Framework\Context;
use Trax\Framework\Repo\Query;
use Trax\Framework\Repo\RepositoryInterface;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\AgentProfiles\Repos\AgentProfile\Actions\OneFunctions;

traxDeclareRepositoryClass('Trax\AgentProfiles\Repos\AgentProfile', 'agent-profiles');

class AgentProfileRepository extends Repository implements RepositoryInterface
{
    use AgentProfileFilters, OneFunctions;

    /**
     * The applicable domain.
     */
    protected $domain = 'agent-profiles';

    /**
     * The applicable scopes.
     */
    protected $scopes = ['store', 'user'];

    /**
     * Return the class of the factory.
     *
     * @return string
     */
    public function factoryClass()
    {
        return AgentProfileFactory::class;
    }

    /**
     * Return the user mine filters.
     *
     * @return array
     */
    public function mineUserFilters(): array
    {
        return [
            ['person' => Context::user()->person_hids]
        ];
    }

    /**
     * Clear agents data.
     *
     * @param  array  $agents    strings ID
     * @return void
     */
    public function clearAgents(array $agents): void
    {
        $ids = array_map(function ($agent) {
            return XapiAgent::hashIdFromStringId($agent);
        }, $agents);

        $this->deleteByQuery(new Query(['filters' => [
            'agent_id' => ['$in' => $ids]
        ]]));
    }
}
