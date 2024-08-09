<?php

namespace Trax\Statements\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Trax\Framework\Tests\Data\Statements;
use Trax\Framework\Xapi\Helpers\Multipart;
use Trax\Statements\Tests\Utils\StandardApi;
use Trax\Statements\Tests\Utils\RecordStatements;

class StandardApiGetTest extends StandardApi
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
            $this->storeApiEndpoint('xapi/statements')
        )->assertOk()->assertJsonCount(2, 'statements')->json()['statements'];

        $this->assertTrue($uuid1 == $statements[1]['id']);
        $this->assertTrue($uuid2 == $statements[0]['id']);
    }

    public function testGetOne()
    {
        Event::fake();

        $uuid = traxUuid();
        
        $this->createStatement(
            Statements::simple(['id' => $uuid])
        );

        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', ['voidedStatementId' => $uuid])
        )->assertStatus(404);

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid])
        )->assertOk()->json();

        $this->assertTrue($uuid == $statement['id']);
    }

    public function testGetWithAlternateRequest()
    {
        Event::fake();

        $uuid = traxUuid();
        
        $this->createStatement(
            Statements::simple(['id' => $uuid])
        );

        $statement = $this->post(
            $this->storeApiEndpoint('xapi/statements', ['method' => 'get']),
            [
                'statementId' => $uuid
            ]
        )->assertOk()->json();

        $this->assertTrue($uuid == $statement['id']);
    }

    public function testGetVoided()
    {
        Event::fake();

        $uuid = traxUuid();
        
        // We create a voided statement.
        
        $this->createStatement(
            Statements::simple(['id' => $uuid]),
            ['voided' => true]
        );

        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid])
        )->assertStatus(404);
    
        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', ['voidedStatementId' => $uuid])
        )->assertOk()->json();

        $this->assertTrue($uuid == $statement['id']);
    }

    public function testGetVoiding()
    {
        Event::fake();

        $uuid = traxUuid();
        
        $this->createStatement(
            Statements::simple(['id' => $uuid]),
            ['voiding' => true]
        );
    
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', ['voidedStatementId' => $uuid])
        )->assertStatus(404);

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid])
        )->assertOk()->json();

        $this->assertTrue($uuid == $statement['id']);
    }

    public function testAgentMboxFilter()
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

        // Test without related param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
            ])
        )->assertOk()->assertJsonCount(2, 'statements');

        // Test with related param set to false.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
                'related_agents' => 'false',
            ])
        )->assertOk()->assertJsonCount(2, 'statements');

        // Test with related param set to true.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
                'related_agents' => 'true',
            ])
        )->assertOk()->assertJsonCount(3, 'statements');
    }

    public function testAgentAccountFilter()
    {
        Event::fake();

        $this->clearStatements();

        $agent = [
            'objectType' => 'Agent',
            'account' => [
                'name' => 'agent1',
                'homePage' => 'http://traxlrs.com',
            ]
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'actor' => $agent,
            ]),
            Statements::simple([
                'actor' => ['objectType' => 'Group', 'member' => [$agent]],
            ]),
            Statements::simple([
                'object' => $agent,
            ]),
            Statements::simple([
                'object' => ['objectType' => 'Group', 'member' => [$agent]],
            ]),
            Statements::simple([
                'context' => ['instructor' => $agent]
            ]),
            Statements::simple([
                'context' => ['instructor' => ['objectType' => 'Group', 'member' => [$agent]]]
            ]),
            Statements::simple([
                'context' => ['team' => ['objectType' => 'Group', 'member' => [$agent]]]
            ]),
            Statements::simple(),
        ]);

        // Test without related param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
            ])
        )->assertOk()->assertJsonCount(2, 'statements');

        // Test with related param set to false.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
                'related_agents' => 'false',
            ])
        )->assertOk()->assertJsonCount(2, 'statements');

        // Test with related param set to true.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
                'related_agents' => 'true',
            ])
        )->assertOk()->assertJsonCount(7, 'statements');
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

        // Test without related param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'activity' => $activity['id'],
            ])
        )->assertOk()->assertJsonCount(1, 'statements');

        // Test with related param set to false.

        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'activity' => $activity['id'],
                'related_activities' => 'false',
            ])
        )->assertOk()->assertJsonCount(1, 'statements');

        // Test with related param set to true.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'activity' => $activity['id'],
                'related_activities' => 'true',
            ])
        )->assertOk()->assertJsonCount(5, 'statements');
    }

    public function testAgentAndActivityFilter()
    {
        Event::fake();

        $this->clearStatements();

        $agent = [
            'objectType' => 'Agent',
            'mbox' => 'mailto:john@doe.com',
        ];

        $activity = [
            'id' => 'http://traxlrs.com/xapi/activities/01',
        ];

        $this->recordStatementsBatch([
            Statements::simple([
                'actor' => $agent,
            ]),
            Statements::simple([
                'object' => $activity,
            ]),
            Statements::simple([
                'actor' => $agent,
                'object' => $activity,
            ]),
            Statements::simple([
                'context' => [
                    'instructor' => $agent,
                ]
            ]),
            Statements::simple([
                'context' => [
                    'contextActivities' => ['parent' => [$activity]],
                ]
            ]),
            Statements::simple([
                'context' => [
                    'instructor' => $agent,
                    'contextActivities' => ['parent' => [$activity]],
                ]
            ]),
        ]);

        // Test without related param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
                'activity' => $activity['id'],
            ])
        )->assertOk()->assertJsonCount(1, 'statements');

        // Test with related param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'agent' => json_encode($agent),
                'activity' => $activity['id'],
                'related_agents' => 'true',
                'related_activities' => 'true',
            ])
        )->assertOk()->assertJsonCount(2, 'statements');
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

        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'verb' => $verb['id'],
            ])
        )->assertOk()->assertJsonCount(1, 'statements');
    }

    public function testRegistrationFilter()
    {
        Event::fake();

        $this->clearStatements();

        $registration = traxUuid();

        $this->recordStatementsBatch([
            Statements::simple([
                'context' => ['registration' => $registration],
            ]),
            Statements::simple(),
        ]);

        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'registration' => $registration,
            ])
        )->assertOk()->assertJsonCount(1, 'statements');
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
            $this->storeApiEndpoint('xapi/statements', [
                'limit' => 2,
            ])
        )->assertOk()->assertJsonCount(2, 'statements')->json()['statements'];

        $firstStored = $statements[1]['stored'];
        $lastStored = $statements[0]['stored'];
        $this->assertTrue($lastStored > $firstStored);

        // Get since first.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'since' => $firstStored,
            ])
        )->assertOk()->assertJsonCount(1, 'statements');

        // Get since second.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'since' => $lastStored,
            ])
        )->assertOk()->assertJsonCount(0, 'statements');

        // Get until first.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'until' => $firstStored,
            ])
        )->assertOk()->assertJsonCount(1, 'statements');

        // Get until second.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'until' => $lastStored,
            ])
        )->assertOk()->assertJsonCount(2, 'statements');
    }

    public function testGetMoreDesc()
    {
        Event::fake();

        $this->clearStatements();

        $uuids = $this->recordStatementsBatch([
            Statements::simple(),
            Statements::simple(),
            Statements::simple(),
        ]);

        // Get the first.
        $response = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'limit' => 1,
            ])
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[2]);
        $this->assertTrue(isset($response['more']));
        $this->assertTrue(!Str::of($response['more'])->startsWith(config('app.url')));

        // Get the second.
        $response = $this->getJson(
            $response['more']
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[1]);
        $this->assertTrue(isset($response['more']));

        // Get the third.
        $response = $this->getJson(
            $response['more']
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[0]);
    }

    public function testGetMoreAsc()
    {
        Event::fake();

        $this->clearStatements();

        $uuids = $this->recordStatementsBatch([
            Statements::simple(),
            Statements::simple(),
            Statements::simple(),
        ]);

        // Get the first.
        $response = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'limit' => 1,
                'ascending' => 'true',
            ])
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[0]);
        $this->assertTrue(isset($response['more']));

        // Get the second.
        $response = $this->getJson(
            $response['more']
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[1]);
        $this->assertTrue(isset($response['more']));

        // Get the third.
        $response = $this->getJson(
            $response['more']
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[2]);
    }

    public function testGetMoreWithFilters()
    {
        Event::fake();

        $this->clearStatements();

        $uuids = $this->recordStatementsBatch([
            Statements::simple([
                'verb' => ['id' => 'http://traxlrs.com/xapi/verbs/01']
            ]),
            Statements::simple([
                'verb' => ['id' => 'http://traxlrs.com/xapi/verbs/02']
            ]),
            Statements::simple([
                'verb' => ['id' => 'http://traxlrs.com/xapi/verbs/01']
            ]),
        ]);

        // Get the first.
        $response = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'limit' => 1,
                'verb' => 'http://traxlrs.com/xapi/verbs/01',
            ])
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[2]);
        $this->assertTrue(isset($response['more']));

        // Get the second.
        $response = $this->getJson(
            $response['more']
        )->assertOk()->assertJsonCount(1, 'statements')->json();

        $this->assertTrue($response['statements'][0]['id'] == $uuids[0]);
    }

    public function testFormats()
    {
        Event::fake();

        $uuid = traxUuid();
        
        $this->createStatement(
            Statements::simple([
                'id' => $uuid,
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                    'definition' => [
                        'name' => [
                            'en' => 'Name',
                            'fr' => 'Nom',
                        ]
                    ],
                ]
            ])
        );

        // Default.

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'statementId' => $uuid,
            ])
        )->assertOk()->json();

        $this->assertTrue(
            isset($statement['object']['definition'])
            && isset($statement['object']['definition']['name'])
            && isset($statement['object']['definition']['name']['en'])
            && isset($statement['object']['definition']['name']['fr'])
        );

        // Exact.

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'statementId' => $uuid,
                'format' => 'exact',
            ])
        )->assertOk()->json();

        $this->assertTrue(
            isset($statement['object']['definition'])
            && isset($statement['object']['definition']['name'])
            && isset($statement['object']['definition']['name']['en'])
            && isset($statement['object']['definition']['name']['fr'])
        );

        // IDs.

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'statementId' => $uuid,
                'format' => 'ids',
            ])
        )->assertOk()->json();

        $this->assertTrue(
            !isset($statement['object']['definition'])
        );

        // Canonical EN.

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'statementId' => $uuid,
                'format' => 'canonical',
            ])
        )->assertOk()->json();

        $this->assertTrue(
            isset($statement['object']['definition'])
            && isset($statement['object']['definition']['name'])
            && isset($statement['object']['definition']['name']['en'])
            && !isset($statement['object']['definition']['name']['fr'])
        );

        // Canonical FR.

        $statement = $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'statementId' => $uuid,
                'format' => 'canonical',
            ]),
            ['Accept-Language' => 'fr']
        )->assertOk()->json();

        $this->assertTrue(
            isset($statement['object']['definition'])
            && isset($statement['object']['definition']['name'])
            && !isset($statement['object']['definition']['name']['en'])
            && isset($statement['object']['definition']['name']['fr'])
        );
    }

    public function testGetAttachments()
    {
        Event::fake();

        $this->clearStatements();

        list($statementPart, $attachmentPart) = Statements::statementAndAttachmentParts();

        $this->recordMultipartStatement($statementPart->content, [$attachmentPart->sha2 => $attachmentPart]);

        // Attachments set to true.
        $response = $this->get(
            $this->storeApiEndpoint('xapi/statements', [
                'limit' => 1,
                'attachments' => 'true',
            ])
        )->assertOk();
        
        $content = $response->getContent();
        $boundary = Multipart::boundary($response->headers->get('Content-Type'));
        $parts = Multipart::parts($content, $boundary);

        $this->assertTrue(count($parts) == 2);
    }

    public function testWrongRequests()
    {
        // Invalid param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/statements', [
                'invalid' => 'param',
            ])
        )->assertStatus(400);
    }

    public function testHeadRequest()
    {
        $server = $this->transformHeadersToServerVars([
            'content-length' => 0,
        ]);

        $this->call('HEAD', $this->storeApiEndpoint('xapi/statements', [
            'limit' => 1
        ]), [], [], [], $server)->assertOk();
    }
}
