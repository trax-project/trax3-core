<?php

namespace Trax\Agents\Repos\Agent;

use Trax\Framework\Context;
use Trax\Framework\Repo\Query;
use Trax\Framework\Repo\RepositoryInterface;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Agents\Repos\Agent\Actions\OneFunctions;
use Trax\Agents\Repos\Agent\Actions\FinalizeAgents;
use Trax\Agents\Repos\Agent\Actions\GetPerson;

traxDeclareRepositoryClass('Trax\Agents\Repos\Agent', 'agents');

class AgentRepository extends Repository implements RepositoryInterface
{
    use AgentFilters, OneFunctions, FinalizeAgents, GetPerson;

    /**
     * The applicable domain.
     */
    protected $domain = 'agents';

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
        return AgentFactory::class;
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
            'id' => ['$in' => $ids]
        ]]));
    }
}
