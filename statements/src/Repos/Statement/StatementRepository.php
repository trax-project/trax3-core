<?php

namespace Trax\Statements\Repos\Statement;

use Trax\Framework\Context;
use Trax\Framework\Repo\RepositoryInterface;
use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Statements\Repos\Statement\Actions\XapiQuery;
use Trax\Statements\Repos\Statement\Actions\ConsistentThrought;
use Trax\Statements\Repos\Statement\Actions\FinalizeStatements;

traxDeclareRepositoryClass('Trax\Statements\Repos\Statement', 'statements');

class StatementRepository extends Repository implements RepositoryInterface
{
    use StatementFilters, XapiQuery, ConsistentThrought, FinalizeStatements;

    /**
     * The applicable domain.
     */
    protected $domain = 'statements';

    /**
     * The applicable scopes.
     */
    protected $scopes = ['store', 'client', 'user'];

    /**
     * Return the class of the factory.
     *
     * @return string
     */
    public function factoryClass()
    {
        return StatementFactory::class;
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
        foreach ($agents as $agent) {
            $this->deleteByQuery(new Query(['filters' => [
                'agent' => XapiAgent::jsonId($agent)
            ]]));
        }
    }
}
