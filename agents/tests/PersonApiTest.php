<?php

namespace Trax\Agents\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Agents\Tests\Utils\ExtendedApi;
use Trax\Agents\Tests\Utils\RecordAgents;
use Trax\Framework\Xapi\Helpers\XapiAgent;

class PersonApiTest extends ExtendedApi
{
    use RecordAgents;

    public function testPersonApi()
    {
        Event::fake();

        // Prepare some agents.

        $this->clearAgents();

        $this->createAgent([
            'mbox' => 'mailto:agent1@traxlrs.com'
        ]);
        $this->createAgent([
            'mbox' => 'mailto:agent2@traxlrs.com'
        ]);
        $this->createAgent([
            'mbox' => 'mailto:agent3@traxlrs.com'
        ]);

        $agents = $this->getJson(
            $this->storeApiEndpoint('agents', [
                'sort' => ['stored'],
            ])
        )->assertOk()->assertJsonCount(3, 'data')->json()['data'];

        // Check the stored.
        $this->assertTrue(
            $agents[0]['stored'] < $agents[1]['stored']
            && $agents[1]['stored'] < $agents[2]['stored']
        );

        // Check the person_id.
        $this->assertTrue(
            $agents[0]['person_id'] !== $agents[1]['person_id']
            && $agents[0]['person_id'] !== $agents[2]['person_id']
            && $agents[1]['person_id'] !== $agents[2]['person_id']
        );

        // Create persons.
        
        $this->postJson(
            $this->storeApiEndpoint('persons'),
            [
                [
                    ['mbox' => 'mailto:agent1@traxlrs.com'],
                    ['mbox' => 'mailto:agent2@traxlrs.com'],
                ]
            ]
        )->assertStatus(204);

        $updated = $this->getJson(
            $this->storeApiEndpoint('agents', [
                'sort' => ['stored'],
            ])
        )->assertOk()->assertJsonCount(3, 'data')->json()['data'];

        // Check the stored.
        $this->assertTrue(
            $agents[0]['stored'] == $updated[0]['stored']
            && $agents[1]['stored'] == $updated[1]['stored']
            && $agents[2]['stored'] == $updated[2]['stored']
        );

        // Check the person_id.
        $this->assertTrue(
            $updated[0]['person_id'] == $updated[1]['person_id']
            && $updated[0]['person_id'] !== $updated[2]['person_id']
        );

        // Get persons.
        $agent = json_decode(json_encode($updated[0]));
        
        $agents = $this->getJson(
            $this->storeApiEndpoint('persons', [
                'agent' => json_encode(XapiAgent::jsonIdByModel($agent)),
            ])
        )->assertOk()->assertJsonCount(2, 'data')->json()['data'];

        $this->assertTrue($agents[0]['mbox'] == 'mailto:agent1@traxlrs.com'
            || $agents[0]['mbox'] == 'mailto:agent2@traxlrs.com');

        $this->assertTrue($agents[1]['mbox'] == 'mailto:agent1@traxlrs.com'
            || $agents[1]['mbox'] == 'mailto:agent2@traxlrs.com');
    }
}
