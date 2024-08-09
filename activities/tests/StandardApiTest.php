<?php

namespace Trax\Activities\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Activities\Tests\Utils\StandardApi;
use Trax\Activities\Tests\Utils\RecordActivities;

class StandardApiTest extends StandardApi
{
    use RecordActivities;

    public function testGetNonExisting()
    {
        Event::fake();

        $this->clearActivities();

        $activity = $this->getJson(
            $this->storeApiEndpoint('xapi/activities', ['activityId' => 'http://traxlrs.com/xapi/activities/01'])
        )->assertOk()->json();

        $this->assertTrue($activity['id'] == 'http://traxlrs.com/xapi/activities/01');
        $this->assertTrue(!isset($activity['definition']));
    }

    public function testGetExisting()
    {
        Event::fake();

        $this->createActivity([
            'id' => 'http://traxlrs.com/xapi/activities/01',
            'definition' => [
                'type' => 'http://traxlrs.com/xapi/types/01',
            ],
        ]);

        $activity = $this->getJson(
            $this->storeApiEndpoint('xapi/activities', ['activityId' => 'http://traxlrs.com/xapi/activities/01'])
        )->assertOk()->json();

        $this->assertTrue($activity['id'] == 'http://traxlrs.com/xapi/activities/01');
        $this->assertTrue(isset($activity['definition']) && isset($activity['definition']['type']));
        $this->assertTrue($activity['definition']['type'] == 'http://traxlrs.com/xapi/types/01');
    }

    public function testAlternateRequest()
    {
        Event::fake();

        $activity = $this->post(
            $this->storeApiEndpoint('xapi/activities', ['method' => 'get']),
            [
                'activityId' => 'http://traxlrs.com/xapi/activities/01'
            ]
        )->assertOk()->json();

        $this->assertTrue($activity['id'] == 'http://traxlrs.com/xapi/activities/01');
    }

    public function testWrongRequests()
    {
        Event::fake();

        // Missing activityId.
        $this->getJson(
            $this->storeApiEndpoint('xapi/activities')
        )->assertStatus(400);

        // Invalid param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/activities', [
                'activityId' => 'http://traxlrs.com/xapi/activities/01',
                'invalid' => 'param',
            ])
        )->assertStatus(400);

        // Unsupported method.
        $this->putJson(
            $this->storeApiEndpoint('xapi/activities', [
                'activityId' => 'http://traxlrs.com/xapi/activities/01',
            ])
        )->assertStatus(403);
    }
}
