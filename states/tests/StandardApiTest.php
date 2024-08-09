<?php

namespace Trax\States\Tests;

use Illuminate\Support\Facades\Event;
use Trax\States\Tests\Utils\StandardApi;
use Trax\States\Tests\Utils\RecordStates;
use Trax\Agents\Tests\Utils\RecordAgents;
use Trax\States\Events\StatesUpdated;
use Trax\States\Events\StatesDeleted;

class StandardApiTest extends StandardApi
{
    use RecordStates, RecordAgents;

    public function testMerging()
    {
        $this->clearStates();

        $activityId = 'http://traxlrs.com/xapi/activities/01';

        $this->createState(
            $activityId,
            $this->john(),
            'state',
            null,
            ['x' => 1, 'y' => 1, 'a' => ['b' => 1]]
        );

        $model = $this->mergeState(
            $activityId,
            $this->john(),
            'state',
            null,
            ['x' => 2, 'z' => 2, 'a' => ['c' => 2]]
        );
        $content = json_decode($model->content);

        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->y == 1);
        $this->assertTrue($content->z == 2);
        $this->assertTrue(!isset($content->a->b));
        $this->assertTrue($content->a->c == 2);
    }

    public function testGet()
    {
        $this->clearStates();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';
        $registration = traxUuid();

        $before = traxIsoNow();

        $state1 = $this->createState(
            $activityId,
            $this->john(),
            'state1',
            $registration,
            ['x' => 1]
        );

        $state2 = $this->createState(
            $activityId,
            $this->john(),
            'state2',
            null,
            ['x' => 2]
        );

        $after = traxIsoNow();

        // Get unknown state.
        $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state3',
            ])
        )->assertStatus(404);
        
        // Get single state without registration.
        $content = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state1',
            ])
        )->assertOk()->json();

        $this->assertTrue($content['x'] == 1);

        // Get single state with registration.
        $content = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state1',
                'registration' => $registration,
            ])
        )->assertOk()->json();

        $this->assertTrue($content['x'] == 1);

        // Get the list of states without registration.
        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
            ])
        )->assertOk()->json();
        $this->assertTrue(count($states) == 2);

        // Get the list of states with registration.
        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'registration' => $registration,
            ])
        )->assertOk()->json();

        $this->assertTrue(count($states) == 1);
        $this->assertTrue($states[0] == 'state1');

        // Get the list of states with since.
        
        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'since' => $before
            ])
        )->assertOk()->assertJsonCount(2);

        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'since' => $state1->stored
            ])
        )->assertOk()->assertJsonCount(1);

        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'since' => $state2->stored
            ])
        )->assertOk()->assertJsonCount(0);

        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'since' => $after
            ])
        )->assertOk()->assertJsonCount(0);

        // Get a list of unknown states.
        $states = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => 'http://traxlrs.com/xapi/activities/02',
                'agent' => $this->john(true),
            ])
        )->assertOk()->json();

        $this->assertTrue(count($states) == 0);
    }

    public function testDelete()
    {
        Event::fake();

        $this->clearStates();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';
        $registration = traxUuid();

        // Delete a single state without registration.

        $this->createState(
            $activityId,
            $this->john(),
            'state1',
            null,
            ['x' => 1]
        );

        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state1',
            ])
        )->assertStatus(204);

        $this->assertTrue(
            $this->states->getOne($activityId, $this->john(), 'state1') === null
        );

        // Delete a single state with registration.

        $this->createState(
            $activityId,
            $this->john(),
            'state1',
            $registration,
            ['x' => 1]
        );

        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state1',
                'registration' => $registration,
            ])
        )->assertStatus(204);

        $this->assertTrue(
            $this->states->getOne($activityId, $this->john(), 'state1') === null
        );

        // Delete the list of states without registration.

        $this->createState(
            $activityId,
            $this->john(),
            'state1',
            $registration,
            ['x' => 1]
        );

        $this->createState(
            $activityId,
            $this->john(),
            'state2',
            null,
            ['x' => 1]
        );

        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
            ])
        )->assertStatus(204);

        $this->assertTrue(
            $this->states->getOne($activityId, $this->john(), 'state1') === null
        );

        // Delete the list of states with registration.

        $this->createState(
            $activityId,
            $this->john(),
            'state1',
            $registration,
            ['x' => 1]
        );

        $this->createState(
            $activityId,
            $this->john(),
            'state2',
            null,
            ['x' => 1]
        );

        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'registration' => $registration,
            ])
        )->assertStatus(204);

        $state = $this->states->getOne($activityId, $this->john(), 'state2');
        $this->assertTrue(!is_null($state));
    
        // Delete an unknown state.

        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state2',
            ])
        )->assertStatus(204);

        if ($this->isLocalService()) {
            Event::assertDispatched(StatesDeleted::class, 5);
        }
    }

    public function testPost()
    {
        Event::fake();

        $this->clearStates();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';

        // First POST.

        $this->postJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            ['x' => 1, 'y' => 1]
        )->assertStatus(204);

        $model = $this->states->getOne($activityId, $this->john(), 'state');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 1);
        $this->assertTrue($content->y == 1);

        // Second POST.
        
        $this->postJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            ['x' => 2, 'z' => 2]
        )->assertStatus(204);

        $model = $this->states->getOne($activityId, $this->john(), 'state');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->y == 1);
        $this->assertTrue($content->z == 2);

        // Third POST.

        $this->postRaw(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            '<b>Bold</b>',
            ['Content-Type' => 'text/html; charset=UTF-8']
        )->assertStatus(400);

        if ($this->isLocalService()) {
            Event::assertDispatched(StatesUpdated::class, 2);
        }
    }

    public function testPut()
    {
        Event::fake();

        $this->clearStates();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';

        // First PUT.

        // Missing concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            ['x' => 1, 'y' => 1]
        )->assertStatus(204);

        // With concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            ['x' => 1, 'y' => 1],
            ['If-None-Match' => '*']
        )->assertStatus(204);

        $model = $this->states->getOne($activityId, $this->john(), 'state');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 1);
        $this->assertTrue($content->y == 1);

        // Second PUT.
        
        // Get the etag.
        $etag = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ])
        )->assertOk()->headers->get('ETag');
        
        // With concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            ['x' => 2, 'z' => 2],
            ['If-Match' => $etag]
        )->assertStatus(204);

        $model = $this->states->getOne($activityId, $this->john(), 'state');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->z == 2);
        $this->assertTrue(!isset($content->y));

        // Third PUT.

        // Get the etag.
        $etag = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ])
        )->assertOk()->headers->get('ETag');
        
        // With concurrency header.
        $this->putRaw(
            $this->storeApiEndpoint('xapi/activities/state', [
                'activityId' => $activityId,
                'agent' => $this->john(true),
                'stateId' => 'state',
            ]),
            '<b>Bold</b>',
            ['Content-Type' => 'text/html; charset=UTF-8', 'If-Match' => $etag]
        )->assertStatus(204);

        $model = $this->states->getOne($activityId, $this->john(), 'state');

        $this->assertTrue($model->content == '<b>Bold</b>');
        $this->assertTrue($model->content_type == 'text/html; charset=UTF-8');

        if ($this->isLocalService()) {
            Event::assertDispatched(StatesUpdated::class, 4);
        }
    }
}
