<?php

namespace Trax\AgentProfiles\Tests;

use Illuminate\Support\Facades\Event;
use Trax\AgentProfiles\Tests\Utils\StandardApi;
use Trax\AgentProfiles\Tests\Utils\RecordAgentProfiles;
use Trax\AgentProfiles\Events\AgentProfilesUpdated;
use Trax\AgentProfiles\Events\AgentProfilesDeleted;
use Trax\Agents\Tests\Utils\RecordAgents;

class StandardApiTest extends StandardApi
{
    use RecordAgentProfiles, RecordAgents;

    public function testMerging()
    {
        $this->clearAgentProfiles();

        $this->createAgentProfile(
            $this->john(),
            'profile',
            ['x' => 1, 'y' => 1, 'a' => ['b' => 1]]
        );

        $model = $this->mergeAgentProfile(
            $this->john(),
            'profile',
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
        $this->clearAgentProfiles();
        
        $before = traxIsoNow();

        $profile1 = $this->createAgentProfile(
            $this->john(),
            'profile1',
            ['x' => 1]
        );

        $profile2 = $this->createAgentProfile(
            $this->john(),
            'profile2',
            ['x' => 2]
        );

        $after = traxIsoNow();

        // Get unknown profile.
        $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile3',
            ])
        )->assertStatus(404);

        // Get single profile.
        $content = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile1',
            ])
        )->assertOk()->json();

        $this->assertTrue($content['x'] == 1);

        // Get the list of profiles.
        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
            ])
        )->assertOk()->assertJsonCount(2);

        // Get the list of profiles since.
        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'since' => $before
            ])
        )->assertOk()->assertJsonCount(2);

        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'since' => $profile1->stored
            ])
        )->assertOk()->assertJsonCount(1);

        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'since' => $profile2->stored
            ])
        )->assertOk()->assertJsonCount(0);

        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'since' => $after
            ])
        )->assertOk()->assertJsonCount(0);

        // Get a list of unknown profiles.
        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->doe(true),
            ])
        )->assertOk()->json();

        $this->assertTrue(count($profiles) == 0);
    }

    public function testDelete()
    {
        Event::fake();

        $this->clearAgentProfiles();
        
        $this->createAgentProfile(
            $this->john(),
            'profile1',
            ['x' => 1]
        );

        // Delete a single profile.
        $this->deleteJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile1',
            ])
        )->assertStatus(204);

        $this->assertTrue(
            $this->profiles->getOne($this->john(), 'profile1') === null
        );

        // Delete an unknown profile.
        $this->deleteJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile2',
            ])
        )->assertStatus(204);

        if ($this->isLocalService()) {
            Event::assertDispatched(AgentProfilesDeleted::class, 2);
        }
    }

    public function testPost()
    {
        Event::fake();

        $this->clearAgentProfiles();
        
        // First POST.

        $this->postJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            ['x' => 1, 'y' => 1]
        )->assertStatus(204);

        $model = $this->profiles->getOne($this->john(), 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 1);
        $this->assertTrue($content->y == 1);

        // Second POST.
        
        $this->postJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            ['x' => 2, 'z' => 2]
        )->assertStatus(204);

        $model = $this->profiles->getOne($this->john(), 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->y == 1);
        $this->assertTrue($content->z == 2);

        // Third POST.

        $this->postRaw(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            '<b>Bold</b>',
            ['Content-Type' => 'text/html; charset=UTF-8']
        )->assertStatus(400);

        if ($this->isLocalService()) {
            Event::assertDispatched(AgentProfilesUpdated::class, 2);
        }
    }

    public function testPut()
    {
        Event::fake();

        $this->clearAgentProfiles();
        
        // First PUT.

        // Missing concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            ['x' => 1, 'y' => 1]
        )->assertStatus(400);

        // With concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            ['x' => 1, 'y' => 1],
            ['If-None-Match' => '*']
        )->assertStatus(204);

        $model = $this->profiles->getOne($this->john(), 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 1);
        $this->assertTrue($content->y == 1);

        // Second PUT.
        
        // Get the etag.
        $etag = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ])
        )->assertOk()->headers->get('ETag');
        
        // With concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            ['x' => 2, 'z' => 2],
            ['If-Match' => $etag]
        )->assertStatus(204);

        $model = $this->profiles->getOne($this->john(), 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->z == 2);
        $this->assertTrue(!isset($content->y));

        // Third PUT.

        // Get the etag.
        $etag = $this->getJson(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ])
        )->assertOk()->headers->get('ETag');
        
        // With concurrency header.
        $this->putRaw(
            $this->storeApiEndpoint('xapi/agents/profile', [
                'agent' => $this->john(true),
                'profileId' => 'profile',
            ]),
            '<b>Bold</b>',
            ['Content-Type' => 'text/html; charset=UTF-8', 'If-Match' => $etag]
        )->assertStatus(204);

        $model = $this->profiles->getOne($this->john(), 'profile');

        $this->assertTrue($model->content == '<b>Bold</b>');
        $this->assertTrue($model->content_type == 'text/html; charset=UTF-8');

        if ($this->isLocalService()) {
            Event::assertDispatched(AgentProfilesUpdated::class, 3);
        }
    }
}
