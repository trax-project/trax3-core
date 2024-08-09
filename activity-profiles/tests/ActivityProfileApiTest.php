<?php

namespace Trax\ActivityProfiles\Tests;

use Illuminate\Support\Facades\Event;
use Trax\ActivityProfiles\Tests\Utils\ExtendedApi;
use Trax\ActivityProfiles\Tests\Utils\RecordActivityProfiles;

class ActivityProfileApiTest extends ExtendedApi
{
    use RecordActivityProfiles;

    public function testGetActivityProfiles()
    {
        Event::fake();

        $this->clearActivityProfiles();

        $this->createActivityProfile(
            'http://traxlrs.com/xapi/activities/01',
            'profile1',
            ['x' => 1]
        );

        $this->createActivityProfile(
            'http://traxlrs.com/xapi/activities/02',
            'profile2',
            'bla bla bla',
            'text/html'
        );

        // Test without filter.
        
        $this->getJson(
            $this->storeApiEndpoint('activity-profiles')
        )->assertOk()->assertJsonCount(2, 'data');

        // Test activityId filter.
        
        $this->getJson(
            $this->storeApiEndpoint('activity-profiles', [
                'filters' => [
                    'activityId' => 'http://traxlrs.com/xapi/activities/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('activity-profiles', [
                'filters' => [
                    'activityId' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.activityId');

        // Test profileId filter.
        
        $this->getJson(
            $this->storeApiEndpoint('activity-profiles', [
                'filters' => [
                    'profileId' => 'profile1',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Test type filter.
        
        $this->getJson(
            $this->storeApiEndpoint('activity-profiles', [
                'filters' => [
                    'type' => 'text/html',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
    }
}
