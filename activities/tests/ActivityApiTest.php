<?php

namespace Trax\Activities\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Activities\Tests\Utils\ExtendedApi;
use Trax\Activities\Tests\Utils\RecordActivities;

class ActivityApiTest extends ExtendedApi
{
    use RecordActivities;

    public function testGetActivities()
    {
        Event::fake();

        $this->clearActivities();

        $this->createActivity([
            'id' => 'http://traxlrs.com/xapi/activities/01',
            'definition' => [
                'type' => 'http://traxlrs.com/xapi/types/01',
                'name' => [
                    'en' => 'Activity One',
                ]
            ],
        ]);

        $this->createActivity([
            'id' => 'http://traxlrs.com/xapi/activities/02',
            'definition' => [
                'type' => 'http://traxlrs.com/xapi/types/02',
                'name' => [
                    'en' => 'Activity Two',
                ]
            ],
        ]);

        $this->createActivity([
            'id' => 'http://traxlrs.com/xapi/activities/03',
        ]);

        $this->getJson(
            $this->storeApiEndpoint('activities')
        )->assertOk()->assertJsonCount(3, 'data');

        // Test ID filter.

        $this->getJson(
            $this->storeApiEndpoint('activities', [
                'filters' => [
                    'activityId' => 'http://traxlrs.com/xapi/activities/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('activities', [
                'filters' => [
                    'activityId' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.activityId');

        // Test type filter.
        
        $this->getJson(
            $this->storeApiEndpoint('activities', [
                'filters' => [
                    'type' => 'http://traxlrs.com/xapi/types/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');
        
        $this->getJson(
            $this->storeApiEndpoint('activities', [
                'filters' => [
                    'type' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.type');
    }
}
