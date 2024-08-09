<?php

namespace Trax\States\Tests;

use Illuminate\Support\Facades\Event;
use Trax\States\Tests\Utils\ExtendedApi;
use Trax\States\Tests\Utils\RecordStates;
use Trax\Agents\Tests\Utils\RecordAgents;

class StateApiTest extends ExtendedApi
{
    use RecordStates, RecordAgents;

    public function testGetStates()
    {
        Event::fake();

        $this->clearStates();

        $this->createState(
            'http://traxlrs.com/xapi/activities/01',
            $this->john(),
            'state1',
            null,
            ['x' => 1, 'y' => 1, 'a' => ['b' => 1]]
        );

        $this->createState(
            'http://traxlrs.com/xapi/activities/02',
            $this->doe(),
            'state2',
            null,
            'bla bla bla',
            'text/html'
        );

        // Test without filter.
        
        $this->getJson(
            $this->storeApiEndpoint('states')
        )->assertOk()->assertJsonCount(2, 'data');

        // Test agent filter.
        
        $this->getJson(
            $this->storeApiEndpoint('states', [
                'filters' => [
                    'agent' => $this->john(true),
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('states', [
                'filters' => [
                    'agent' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.agent');

        // Test activityId filter.
        
        $this->getJson(
            $this->storeApiEndpoint('states', [
                'filters' => [
                    'activityId' => 'http://traxlrs.com/xapi/activities/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('states', [
                'filters' => [
                    'activityId' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.activityId');

        // Test stateId filter.
        
        $this->getJson(
            $this->storeApiEndpoint('states', [
                'filters' => [
                    'stateId' => 'state1',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Test type filter.
        
        $this->getJson(
            $this->storeApiEndpoint('states', [
                'filters' => [
                    'type' => 'text/html',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
    }
}
