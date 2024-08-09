<?php

namespace Trax\Agents\Tests\Utils;

use Trax\Framework\Context;
use Trax\Agents\Repos\Agent\AgentRepository;
use Trax\Agents\Recording\AgentUpdater;
use Trax\Agents\Recording\PersonRecorder;
use Trax\Framework\Xapi\Helpers\Json;

trait RecordAgents
{
    protected function clearAgents(string $store = 'default')
    {
        Context::setStore($store);
        
        app(AgentRepository::class)->clear();
    }

    protected function recordPerson(array $agents, string $store = 'default')
    {
        Context::setStore($store);

        $person = collect($agents)->map(function ($agent) {
            return Json::object($agent);
        })->all();

        app(PersonRecorder::class)->record([$person]);

        return app(AgentRepository::class)->getOne($agents[0])->person_id;
    }

    protected function createAgent(array $agent, string $store = 'default')
    {
        Context::setStore($store);

        // Remove the agent.
        app(AgentRepository::class)->deleteOne($agent);
        
        // Create the agent.
        app(AgentRepository::class)->insert([$agent], 'prepareXapiAgent');

        // Return the agent.
        return app(AgentRepository::class)->getOne($agent);
    }

    protected function recordStatementsMock(array $statements, string $store = 'default')
    {
        Context::setStore($store);

        collect($statements)->each(function ($statement) {
            app(AgentUpdater::class)->update(
                collect([['raw' => json_encode($statement)]])
            );
        });
    }

    protected function recordStatementsBatchMock(array $statements, string $store = 'default')
    {
        Context::setStore($store);

        $batch = collect($statements)->map(function ($statement) {
            return ['raw' => json_encode($statement)];
        });

        app(AgentUpdater::class)->update($batch);
    }

    protected function john(bool $encode = false)
    {
        $john = [
            'name' => 'John',
            'account' => [
                'name' => 'john',
                'homePage' => 'http://traxlrs.com'
            ]
        ];
        return $encode ? json_encode($john) : $john;
    }

    protected function doe(bool $encode = false)
    {
        $john = [
            'name' => 'Doe',
            'account' => [
                'name' => 'doe',
                'homePage' => 'http://traxlrs.com'
            ]
        ];
        return $encode ? json_encode($john) : $john;
    }
}
