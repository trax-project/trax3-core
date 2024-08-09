<?php

namespace Trax\ActivityProfiles\Tests;

use Illuminate\Support\Facades\Event;
use Trax\ActivityProfiles\Tests\Utils\StandardApi;
use Trax\ActivityProfiles\Tests\Utils\RecordActivityProfiles;
use Trax\ActivityProfiles\Events\ActivityProfilesUpdated;
use Trax\ActivityProfiles\Events\ActivityProfilesDeleted;

class StandardApiTest extends StandardApi
{
    use RecordActivityProfiles;

    public function testMerging()
    {
        $this->clearActivityProfiles();

        $activityId = 'http://traxlrs.com/xapi/activities/01';

        $this->createActivityProfile(
            $activityId,
            'profile',
            ['x' => 1, 'y' => 1, 'a' => ['b' => 1]]
        );

        $model = $this->mergeActivityProfile(
            $activityId,
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
        $this->clearActivityProfiles();

        $activityId = 'http://traxlrs.com/xapi/activities/01';

        $before = traxIsoNow();

        $profile1 = $this->createActivityProfile(
            $activityId,
            'profile1',
            ['x' => 1]
        );

        $profile2 = $this->createActivityProfile(
            $activityId,
            'profile2',
            ['x' => 2]
        );

        $after = traxIsoNow();

        // Get unknown profile.
        $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile3',
            ])
        )->assertStatus(404);

        // Get single profile.
        $content = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile1',
            ])
        )->assertOk()->json();

        $this->assertTrue($content['x'] == 1);

        // Get the list of profiles.
        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
            ])
        )->assertOk()->assertJsonCount(2);

        // Get the list of profiles since.
        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'since' => $before
            ])
        )->assertOk()->assertJsonCount(2);

        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'since' => $profile1->stored
            ])
        )->assertOk()->assertJsonCount(1);

        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'since' => $profile2->stored
            ])
        )->assertOk()->assertJsonCount(0);

        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'since' => $after
            ])
        )->assertOk()->assertJsonCount(0);

        // Get a list of unknown profiles.
        $profiles = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => 'http://traxlrs.com/xapi/activities/02',
            ])
        )->assertOk()->json();

        $this->assertTrue(count($profiles) == 0);
    }

    public function testDelete()
    {
        Event::fake();

        $this->clearActivityProfiles();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';

        $this->createActivityProfile(
            $activityId,
            'profile1',
            ['x' => 1]
        );

        // Delete a single profile.
        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile1',
            ])
        )->assertStatus(204);

        $this->assertTrue(
            $this->profiles->getOne($activityId, 'profile1') === null
        );

        // Delete an unknown profile.
        $this->deleteJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile2',
            ])
        )->assertStatus(204);

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivityProfilesDeleted::class, 2);
        }
    }

    public function testPost()
    {
        Event::fake();

        $this->clearActivityProfiles();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';

        // First POST.

        $this->postJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            ['x' => 1, 'y' => 1]
        )->assertStatus(204);

        $model = $this->profiles->getOne($activityId, 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 1);
        $this->assertTrue($content->y == 1);

        // Second POST.
        
        $this->postJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            ['x' => 2, 'z' => 2]
        )->assertStatus(204);

        $model = $this->profiles->getOne($activityId, 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->y == 1);
        $this->assertTrue($content->z == 2);

        // Third POST.

        $this->postRaw(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            '<b>Bold</b>',
            ['Content-Type' => 'text/html; charset=UTF-8']
        )->assertStatus(400);

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivityProfilesUpdated::class, 2);
        }
    }

    public function testPut()
    {
        Event::fake();

        $this->clearActivityProfiles();
        
        $activityId = 'http://traxlrs.com/xapi/activities/01';

        // First PUT.

        // Missing concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            ['x' => 1, 'y' => 1]
        )->assertStatus(400);

        // With concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            ['x' => 1, 'y' => 1],
            ['If-None-Match' => '*']
        )->assertStatus(204);

        $model = $this->profiles->getOne($activityId, 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 1);
        $this->assertTrue($content->y == 1);

        // Second PUT.
        
        // Get the etag.
        $etag = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ])
        )->assertOk()->headers->get('ETag');
        
        // With concurrency header.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            ['x' => 2, 'z' => 2],
            ['If-Match' => $etag]
        )->assertStatus(204);

        $model = $this->profiles->getOne($activityId, 'profile');
        $content = json_decode($model->content);

        $this->assertTrue($model->content_type == 'application/json');
        $this->assertTrue($content->x == 2);
        $this->assertTrue($content->z == 2);
        $this->assertTrue(!isset($content->y));

        // Third PUT.

        // Get the etag.
        $etag = $this->getJson(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ])
        )->assertOk()->headers->get('ETag');
        
        // With concurrency header.
        $this->putRaw(
            $this->storeApiEndpoint('xapi/activities/profile', [
                'activityId' => $activityId,
                'profileId' => 'profile',
            ]),
            '<b>Bold</b>',
            ['Content-Type' => 'text/html; charset=UTF-8', 'If-Match' => $etag]
        )->assertStatus(204);

        $model = $this->profiles->getOne($activityId, 'profile');

        $this->assertTrue($model->content == '<b>Bold</b>');
        $this->assertTrue($model->content_type == 'text/html; charset=UTF-8');

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivityProfilesUpdated::class, 3);
        }
    }
}
