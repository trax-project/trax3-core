<?php

namespace Trax\Statements\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Framework\Tests\Data\Statements;
use Trax\Statements\Tests\Utils\ExtendedApi;
use Trax\Statements\Tests\Utils\RecordStatements;

class StatementApiTest extends ExtendedApi
{
    use RecordStatements;

    public function testGetBatch()
    {
        Event::fake();

        $this->clearStatements();

        $uuid1 = traxUuid();
        $uuid2 = traxUuid();
        
        $this->recordStatementsBatch([
            Statements::simple(['id' => $uuid1]),
            Statements::simple(['id' => $uuid2])
        ]);

        $statements = $this->getJson(
            $this->storeApiEndpoint('statements', ['sort' => ['stored']])
        )->assertOk()->assertJsonCount(2, 'data')->json()['data'];

        $this->assertTrue($uuid1 == $statements[0]['id']);
        $this->assertTrue($uuid2 == $statements[1]['id']);
    }


    public function testAgentFilter()
    {
        Event::fake();

        $this->clearStatements();

        $agent = [
            'objectType' => 'Agent',
            'mbox' => 'mailto:john@doe.com',
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'actor' => $agent,
            ]),
            Statements::simple([
                'object' => $agent,
            ]),
            Statements::simple([
                'context' => [
                    'instructor' => $agent,
                ]
            ]),
            Statements::simple(),
        ]);

        // Test without location.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'agent' => json_encode($agent),
                ],
            ])
        )->assertOk()->assertJsonCount(2, 'data');

        // Test with actor.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'agent' => json_encode($agent),
                ],
                'options' => [
                    'agent_location' => 'actor',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Test with object.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'agent' => json_encode($agent),
                ],
                'options' => [
                    'agent_location' => 'object',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Test with everywhere.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'agent' => json_encode($agent),
                ],
                'options' => [
                    'agent_location' => 'everywhere',
                ],
            ])
        )->assertOk()->assertJsonCount(3, 'data');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'agent' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.agent');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'options' => [
                    'agent_location' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.agent_location');
    }

    public function testVerbFilter()
    {
        Event::fake();

        $this->clearStatements();

        $verb = [
            'id' => 'http://traxlrs.com/xapi/verbs/01',
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'verb' => $verb,
            ]),
            Statements::simple(),
        ]);

        // Test with object.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'verb' => $verb['id'],
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'verb' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.verb');
    }

    public function testActivityFilter()
    {
        Event::fake();

        $this->clearStatements();

        $activity = [
            'id' => 'http://traxlrs.com/xapi/activities/01',
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'object' => $activity,
            ]),
            Statements::simple([
                'context' => ['contextActivities' => ['parent' => [$activity]]]
            ]),
            Statements::simple([
                'context' => ['contextActivities' => ['grouping' => [$activity]]]
            ]),
            Statements::simple([
                'context' => ['contextActivities' => ['category' => [$activity]]]
            ]),
            Statements::simple([
                'context' => ['contextActivities' => ['other' => [$activity]]]
            ]),
            Statements::simple(),
        ]);

        // Test with object.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'activity' => $activity['id'],
                ],
                'options' => [
                    'activity_location' => 'object',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Test with everywhere.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'activity' => $activity['id'],
                ],
                'options' => [
                    'activity_location' => 'everywhere',
                ],
            ])
        )->assertOk()->assertJsonCount(5, 'data');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'activity' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.activity');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'options' => [
                    'activity_location' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.activity_location');
    }

    public function testTypeFilter()
    {
        Event::fake();

        $this->clearStatements();

        $activity1 = [
            'id' => 'http://traxlrs.com/xapi/activities/01',
            'definition' => [
                'type' => 'http://traxlrs.com/xapi/types/01'
            ]
        ];

        $activity2 = [
            'id' => 'http://traxlrs.com/xapi/activities/02',
            'definition' => [
                'type' => 'http://traxlrs.com/xapi/types/02'
            ]
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'object' => $activity1,
            ]),
            Statements::simple([
                'object' => $activity2,
            ]),
            Statements::simple(),
        ]);

        // Test with object.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'type' => 'http://traxlrs.com/xapi/types/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'type' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.type');
    }

    public function testProfileFilter()
    {
        Event::fake();

        $this->clearStatements();

        $profile = [
            'id' => 'http://traxlrs.com/xapi/profiles/01',
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'context' => [
                    'contextActivities' => [
                        'category' => [$profile]
                    ]
                ],
            ]),
            Statements::simple(),
        ]);

        // Test with object.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'profile' => 'http://traxlrs.com/xapi/profiles/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'profile' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.profile');
    }

    public function testSinceUntilFilters()
    {
        Event::fake();

        $this->clearStatements();

        $this->createStatements([
            Statements::simple(),
            Statements::simple(),
        ]);

        $statements = $this->getJson(
            $this->storeApiEndpoint('statements', [
                'sort' => ['stored']
            ])
        )->assertOk()->assertJsonCount(2, 'data')->json()['data'];

        $firstStored = $statements[0]['stored'];
        $lastStored = $statements[1]['stored'];
        $this->assertTrue($lastStored > $firstStored);

        // Get since first.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'since' => $firstStored,
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Get since second.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'since' => $lastStored,
                ],
            ])
        )->assertOk()->assertJsonCount(0, 'data');

        // Get until first.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'until' => $firstStored,
                ],
            ])
        )->assertOk()->assertJsonCount(1, 'data');

        // Get until second.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'until' => $lastStored,
                ],
            ])
        )->assertOk()->assertJsonCount(2, 'data');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'since' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.since');

        // Error.
        $this->getJson(
            $this->storeApiEndpoint('statements', [
                'filters' => [
                    'until' => 'invalid',
                ],
            ])
        )->assertStatus(422)->assertJsonCount(1, 'errors.until');
    }
}
