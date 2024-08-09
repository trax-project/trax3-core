<?php

namespace Trax\Agents\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Agents\Tests\Utils\ExtendedApi;
use Trax\Agents\Tests\Utils\RecordAgents;

class AgentApiTest extends ExtendedApi
{
    use RecordAgents;

    public function testGet()
    {
        Event::fake();

        $this->clearAgents();

        $this->createAgent([
            'name' => 'John',
            'account' => [
                'name' => 'john',
                'homePage' => 'http://traxlrs.com'
            ],
        ]);

        $this->createAgent([
            'name' => 'Jack',
            'mbox' => 'mailto:john.doe@traxlrs.com'
        ]);

        $this->createAgent([
            'objectType' => 'Group',
            'name' => 'Group A',
            'mbox' => 'mailto:groupA@traxlrs.com',
        ]);

        $this->createAgent([
            'objectType' => 'Group',
            'name' => 'Group B',
            'mbox' => 'mailto:groupB@traxlrs.com',
            'member' => [
                [
                    'name' => 'John',
                    'account' => [
                        'name' => 'john',
                        'homePage' => 'http://traxlrs.com'
                    ]
                ],
                [
                    'name' => 'Jack',
                    'mbox' => 'mailto:john.doe@traxlrs.com'
                ],
            ],
        ]);

        // Test without filter.
        $this->getJson(
            $this->storeApiEndpoint('agents')
        )->assertOk()->assertJsonCount(4, 'data');

        // Test agent filter.
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => json_encode(['mbox' => 'mailto:john.doe@traxlrs.com']),
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => json_encode(['mbox' => 'mailto:john.doe@traxlrs.com']),
                ],
                'options' => [
                    'location' => 'agent',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => json_encode(['mbox' => 'mailto:john.doe@traxlrs.com']),
                ],
                'options' => [
                    'location' => 'group',
                ],
            ])
        )->assertOk()->assertJsonCount(0, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => json_encode(['mbox' => 'mailto:groupA@traxlrs.com']),
                ],
                'options' => [
                    'location' => 'group',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => json_encode(['mbox' => 'mailto:john.doe@traxlrs.com']),
                ],
                'options' => [
                    'location' => 'member',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => json_encode(['mbox' => 'mailto:john.doe@traxlrs.com']),
                ],
                'options' => [
                    'location' => 'all',
                ],
            ])
        )->assertOk()->assertJsonCount(2, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'agent' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.agent');

        // Test type filter.
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'type' => 'all',
                ],
            ])
        )->assertOk()->assertJsonCount(4, 'data');

        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'type' => 'agents',
                ],
            ])
        )->assertOk()->assertJsonCount(2, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'type' => 'groups',
                ],
            ])
        )->assertOk()->assertJsonCount(2, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'type' => 'groups_with_members',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        $this->getJson(
            $this->storeApiEndpoint('agents', [
                'filters' => [
                    'type' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.type');
    }
}
