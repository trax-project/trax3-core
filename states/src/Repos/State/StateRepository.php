<?php

namespace Trax\States\Repos\State;

use Trax\Framework\Context;
use Trax\Framework\Repo\Query;
use Trax\Framework\Repo\RepositoryInterface;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\States\Repos\State\Actions\OneFunctions;

traxDeclareRepositoryClass('Trax\States\Repos\State', 'states');

class StateRepository extends Repository implements RepositoryInterface
{
    use StateFilters, OneFunctions;

    /**
     * The applicable domain.
     */
    protected $domain = 'states';

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
        return StateFactory::class;
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
