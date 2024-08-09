<?php

namespace Trax\AgentProfiles\Tests;

use Illuminate\Support\Facades\Event;
use Trax\AgentProfiles\Tests\Utils\ExtendedApi;
use Trax\AgentProfiles\Tests\Utils\RecordAgentProfiles;
use Trax\Agents\Tests\Utils\RecordAgents;

class AgentProfileApiTest extends ExtendedApi
{
    use RecordAgentProfiles, RecordAgents;

    public function testGetAgentProfiles()
    {
        Event::fake();

        $this->clearAgentProfiles();

        $this->createAgentProfile(
            $this->john(),
            'profile1',
            ['x' => 1, 'y' => 1, 'a' => ['b' => 1]]
        );

        $this->createAgentProfile(
            $this->doe(),
            'profile2',
            'bla bla bla',
            'text/html'
        );

        // Test without filter.
        $this->getJson(
            $this->storeApiEndpoint('agent-profiles')
        )->assertOk()->assertJsonCount(2, 'data');

        // Test agent filter.
        
        $this->getJson(
            $this->storeApiEndpoint('agent-profiles', [
                'filters' => [
                    'agent' => $this->john(true),
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agent-profiles', [
                'filters' => [
                    'agent' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.agent');

        // Test profileId filter.
        
        $this->getJson(
            $this->storeApiEndpoint('agent-profiles', [
                'filters' => [
                    'profileId' => 'profile1',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Test type filter.
        
        $this->getJson(
            $this->storeApiEndpoint('agent-profiles', [
                'filters' => [
                    'type' => 'text/html',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
    }
}
