<?php

namespace Trax\Activities\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Framework\Tests\Data\Statements;
use Trax\Framework\Xapi\Helpers\XapiActivity;
use Trax\Framework\Xapi\Helpers\XapiType;
use Trax\Activities\Events\ActivitiesUpdated;
use Trax\Activities\Tests\Utils\StandardApi;
use Trax\Activities\Tests\Utils\RecordActivities;
use Trax\Statements\Tests\Utils\RecordStatements;

class ActivityUpdateTest extends StandardApi
{
    use RecordActivities, RecordStatements;

    public function testCreation()
    {
        $activity = $this->createActivity([
            'id' => 'http://traxlrs.com/xapi/activities/01'
        ]);

        $this->assertTrue($activity->id == XapiActivity::hashId('http://traxlrs.com/xapi/activities/01'));
        $this->assertTrue($activity->iri == 'http://traxlrs.com/xapi/activities/01');
        $this->assertTrue(empty((array) $activity->definition));
    }

    public function testMerging1()
    {
        $activity = $this->updateActivity([
            [
                'id' => 'http://traxlrs.com/xapi/activities/01',
            ],
            [
                'id' => 'http://traxlrs.com/xapi/activities/01',
                'definition' => [
                    'type' => 'http://traxlrs.com/xapi/types/02',
                ],
            ]
        ]);

        $this->assertTrue(!empty((array) $activity->definition));
        $this->assertTrue($activity->definition->type == 'http://traxlrs.com/xapi/types/02');
        $this->assertTrue($activity->type_id == XapiType::hashId('http://traxlrs.com/xapi/types/02'));
    }

    public function testMerging2()
    {
        $activity = $this->updateActivity([
            [
                'id' => 'http://traxlrs.com/xapi/activities/01',
                'definition' => [
                    'type' => 'http://traxlrs.com/xapi/types/01',
                    'name' => [
                        'fr' => 'Activité 1',
                        'es' => 'Actividad 01',
                    ],
                    'extensions' => [
                        'http://traxlrs.com/xapi/extensions/01' => 1,
                        'http://traxlrs.com/xapi/extensions/02' => [
                            'prop1' => 1,
                            'prop2' => 2,
                        ],
                    ],
                ],
            ],
            [
                'id' => 'http://traxlrs.com/xapi/activities/01',
                'definition' => [
                    'type' => 'http://traxlrs.com/xapi/types/02',
                    'name' => [
                        'fr' => 'Activité 01',
                        'en' => 'Activity 01',
                    ],
                    'extensions' => [
                        'http://traxlrs.com/xapi/extensions/01' => 2,
                        'http://traxlrs.com/xapi/extensions/02' => [
                            'prop3' => 3,
                        ],
                    ],
                ],
            ]
        ]);

        $this->assertTrue($activity->definition->type == 'http://traxlrs.com/xapi/types/02');
        $this->assertTrue($activity->definition->name->fr == 'Activité 01');
        $this->assertTrue($activity->definition->name->es == 'Actividad 01');
        $this->assertTrue($activity->definition->name->en == 'Activity 01');
        $this->assertTrue($activity->definition->extensions->{'http://traxlrs.com/xapi/extensions/01'} == 2);
        $this->assertTrue(!isset($activity->definition->extensions->{'http://traxlrs.com/xapi/extensions/02'}->prop1));
        $this->assertTrue(!isset($activity->definition->extensions->{'http://traxlrs.com/xapi/extensions/02'}->prop2));
        $this->assertTrue($activity->definition->extensions->{'http://traxlrs.com/xapi/extensions/02'}->prop3 == 3);
    }

    public function testIndividualActivities()
    {
        Event::fake();

        $this->clearActivities();

        $this->recordStatementsMock([
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/01',
                    ],
                ],
                'context' => [
                    'contextActivities' => [
                        'category' => [[
                            'id' => 'http://traxlrs.com/xapi/categories/01',
                            'definition' => [
                                'type' => 'http://adlnet.gov/expapi/activities/profile'
                            ]
                        ]],
                    ],
                ],
            ], true),
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/02',
                    ],
                ],
                'context' => [
                    'contextActivities' => [
                        'category' => [[
                            'id' => 'http://traxlrs.com/xapi/categories/02',
                        ]],
                    ],
                ],
            ], true),
        ]);

        // Count activities
        $this->assertTrue($this->activities->count() == 3);
        
        // Check the activity 01.
        $activity = $this->activities->getOne('http://traxlrs.com/xapi/activities/01');
        $this->assertTrue($activity->definition->type == 'http://traxlrs.com/xapi/types/02');
        
        // Check the category 01.
        $activity = $this->activities->getOne('http://traxlrs.com/xapi/categories/01');
        $this->assertTrue($activity->is_category == true);
        $this->assertTrue($activity->is_profile == true);
        
        // Check the category 02.
        $activity = $this->activities->getOne('http://traxlrs.com/xapi/categories/02');
        $this->assertTrue($activity->is_category == true);
        $this->assertTrue($activity->is_profile == false);

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivitiesUpdated::class, 2);
        }
    }

    public function testBatchActivities()
    {
        Event::fake();

        $this->clearActivities();

        $this->recordStatementsBatchMock([
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/01',
                    ],
                ]
            ], true),
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/02',
                    ],
                ]
            ], true),
        ]);

        $activity = $this->activities->getOne('http://traxlrs.com/xapi/activities/01');
        $this->assertTrue($activity->definition->type == 'http://traxlrs.com/xapi/types/02');

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivitiesUpdated::class, 1);
        }
    }

    public function testMixInsertAndUpdate()
    {
        Event::fake();

        $this->clearActivities();

        $this->recordStatementsBatchMock([
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/01',
                    ],
                ]
            ], true),
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/02',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/02',
                    ],
                ]
            ], true),
        ]);
        
        $this->recordStatementsBatchMock([
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/02',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/02bis',
                    ],
                ]
            ], true),
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/03',
                    'definition' => [
                        'type' => 'http://traxlrs.com/xapi/types/03',
                    ],
                ]
            ], true),
        ]);

        $this->assertTrue(
            $this->activities->getOne('http://traxlrs.com/xapi/activities/01')->definition->type == 'http://traxlrs.com/xapi/types/01'
        );
        $this->assertTrue(
            $this->activities->getOne('http://traxlrs.com/xapi/activities/02')->definition->type == 'http://traxlrs.com/xapi/types/02bis'
        );
        $this->assertTrue(
            $this->activities->getOne('http://traxlrs.com/xapi/activities/03')->definition->type == 'http://traxlrs.com/xapi/types/03'
        );

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivitiesUpdated::class, 2);
        }
    }

    public function testEmptyDefinition()
    {
        Event::fake();

        $this->clearActivities();

        $this->recordStatementsMock([
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                ]
            ], true),
        ]);

        if ($this->isLocalService()) {
            Event::assertDispatched(ActivitiesUpdated::class, 1);
        }
    }

    public function testComplexStatement()
    {
        Event::fake();

        $this->clearActivities();

        $this->recordStatementsMock([
            Statements::simple([
                'object' => [
                    'objectType' => 'SubStatement',
                    'actor' => [
                        'objectType' => 'Agent',
                        'name' => 'xAPI account',
                        'mbox' => 'mailto:b17834cb-beb6-4297-a5ca-7bda92794856@adlnet.gov'
                    ],
                    'verb' => [
                        'id' => 'http://adlnet.gov/expapi/verbs/reportedb3744052-e92e-4d45-8888-f2fb46cedd31',
                        'display' => ['en-GB' => 'reported','en-US' => 'reported']
                    ],
                    'context' => [
                        'registration' => 'ec531277-b57b-4c15-8d91-d292c5b2b8f7',
                        'platform' => 'Example virtual meeting software',
                        'language' => 'tlh',
                        'statement' => [
                            'objectType' => 'StatementRef',
                            'id' => '6690e6c9-3ef0-4ed3-8b37-7f3964730bee'
                        ],
                        'contextActivities' => [
                            'category' => [[
                                'objectType' => 'Activity',
                                'id' => 'http://www.example.com/test/array/statements/sub',
                                'definition' => [
                                    'name' => [
                                        'en-GB' => 'example meeting',
                                        'en-US' => 'example meeting'
                                    ],
                                    'description' => [
                                        'en-GB' => 'An example meeting that happened on a specific occasion with certain people present.',
                                        'en-US' => 'An example meeting that happened on a specific occasion with certain people present.'
                                    ],
                                    'moreInfo' => 'http://virtualmeeting.example.com/345256',
                                    'extensions' => [
                                        'http://example.com/profiles/meetings/extension/location' => 'X:\\\\meetings\\\\minutes\\\\examplemeeting.one',
                                        'http://example.com/profiles/meetings/extension/reporter' => [
                                            'name' => 'Thomas','id' => 'http://openid.com/342'
                                        ]
                                    ]
                                ]
                            ]]
                        ],
                        'instructor' => [
                            'objectType' => 'Agent',
                            'name' => 'xAPI mbox',
                            'mbox' => 'mailto:sub@adlnet.gov'
                        ]
                    ],
                    'object' => [
                        'objectType' => 'Activity',
                        'id' => 'http://www.example.com/meetings/occurances/3453456435934-899f-45b0-90d3-1ec0381637a0'
                    ]
                ]
            ], true)
        ]);

        // We don't need to check that we are performing local tests because the events are dispatched by the mockery,
        // so locally, not by the statements service.
        Event::assertDispatched(ActivitiesUpdated::class, 1);
    }

    public function testUpdateFromStatementsApi()
    {
        $this->clearActivities();

        // We make a real POST on the statements API
        // because if the Statements and Activities APIs are located in the same container,
        // the statements API must be targetted in this container to emit a local event inside this container.
        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            Statements::simple([
                'object' => [
                    'id' => 'http://traxlrs.com/xapi/activities/01',
                ],
            ])
        )->assertOk()->assertJsonCount(1);
        
        if ($this->isRemoteService()) {
            sleep(1);
        }

        // Count activities
        $this->assertTrue($this->activities->count() == 1);
        
        // Check the activity 01.
        $activity = $this->activities->getOne('http://traxlrs.com/xapi/activities/01');
        $this->assertTrue($activity->iri == 'http://traxlrs.com/xapi/activities/01');
    }
}
