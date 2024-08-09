<?php

namespace Trax\Agents\Tests;

use Illuminate\Support\Facades\Event;
use Trax\Framework\Tests\Data\Statements;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Agents\Tests\Utils\StandardApi;
use Trax\Agents\Tests\Utils\RecordAgents;
use Trax\Agents\Events\AgentsUpdated;
use Trax\Statements\Tests\Utils\RecordStatements;

class AgentUpdateTest extends StandardApi
{
    use RecordAgents, RecordStatements;

    public function testCreation()
    {
        Event::fake();

        $this->clearAgents();

        // Agent.
        $agent = $this->createAgent($this->john());

        $this->assertTrue($agent->id == XapiAgent::hashId($this->john()));
        $this->assertTrue($agent->sid_type == 'account');
        $this->assertTrue($agent->sid_field_1 == 'john');
        $this->assertTrue($agent->sid_field_2 == 'http://traxlrs.com');
        $this->assertTrue($agent->name == 'John');
        $this->assertTrue($agent->is_group == false);
        $this->assertTrue(is_array($agent->members) && count($agent->members) == 0);

        // Group with members.
        $groupA = [
            'objectType' => 'Group',
            'name' => 'Group A',
            'mbox' => 'mailto:group@traxlrs.com',
            'member' => [
                $this->john()
            ]
        ];
        $group = $this->createAgent($groupA);
        $this->assertTrue($group->id == XapiAgent::hashId($groupA));
        $this->assertTrue($group->sid_type == 'mbox');
        $this->assertTrue($group->sid_field_1 == 'mailto:group@traxlrs.com');
        $this->assertTrue($group->sid_field_2 == null);
        $this->assertTrue($group->name == 'Group A');
        $this->assertTrue($group->is_group == true);
        $this->assertTrue(is_array($group->members) && count($group->members) == 1);
        $this->assertTrue($group->members[0] == XapiAgent::hashId($this->john()));
        $this->assertTrue($group->members_count == 1);
    }

    public function testIndividualAgents()
    {
        Event::fake();

        $this->clearAgents();

        $this->recordStatementsMock([
            Statements::simple([
                'actor' => [
                    'objectType' => 'Group',
                    'name' => 'Promo 2013',
                    'mbox' => 'mailto:promo@traxlrs.com',
                    'member' => [
                        [
                            'mbox' => 'mailto:learner1@traxlrs.com',
                        ],
                    ]
                ]
            ], true),
            Statements::simple([
                'actor' => [
                    'objectType' => 'Group',
                    'name' => 'Promo 2013-S1',
                    'mbox' => 'mailto:promo@traxlrs.com',
                    'member' => [
                        [
                            'mbox' => 'mailto:learner1@traxlrs.com',
                            'name' => 'Learner 1',
                        ],
                        [
                            'mbox' => 'mailto:learner2@traxlrs.com',
                        ],
                    ]
                ]
            ], true),
            Statements::simple([
                'actor' => [
                    'objectType' => 'Group',
                    'name' => 'Promo 2013-S2',
                    'member' => [
                        [
                            'mbox' => 'mailto:learner3@traxlrs.com',
                        ],
                    ]
                ]
            ], true),
        ]);

        // Count.
        $this->assertTrue($this->agents->count() == 4);

        // Check group.
        $agent = $this->agents->getOne(['mbox' => 'mailto:promo@traxlrs.com']);
        $this->assertTrue($agent->name == 'Promo 2013-S1');
        $this->assertTrue(count($agent->members) == 2);

        // Check learner 1.
        $agent = $this->agents->getOne(['mbox' => 'mailto:learner1@traxlrs.com']);
        $this->assertTrue($agent->name == 'Learner 1');

        // Check learner 2.
        $agent = $this->agents->getOne(['mbox' => 'mailto:learner2@traxlrs.com']);
        $this->assertTrue(!is_null($agent));

        // Check learner 3.
        $agent = $this->agents->getOne(['mbox' => 'mailto:learner3@traxlrs.com']);
        $this->assertTrue(!is_null($agent));

        // Check event.
        if ($this->isLocalService()) {
            Event::assertDispatched(AgentsUpdated::class, 3);
        }
    }

    public function testBatchAgents()
    {
        Event::fake();

        $this->clearAgents();
        
        $this->recordStatementsBatchMock([
            Statements::simple([
                'actor' => [
                    'objectType' => 'Group',
                    'name' => 'Promo 2013',
                    'mbox' => 'mailto:promo@traxlrs.com',
                    'member' => [
                        [
                            'mbox' => 'mailto:learner1@traxlrs.com',
                        ],
                    ]
                ]
            ], true),
            Statements::simple([
                'actor' => [
                    'objectType' => 'Group',
                    'name' => 'Promo 2013-S1',
                    'mbox' => 'mailto:promo@traxlrs.com',
                    'member' => [
                        [
                            'mbox' => 'mailto:learner1@traxlrs.com',
                            'name' => 'Learner 1',
                        ],
                        [
                            'mbox' => 'mailto:learner2@traxlrs.com',
                        ],
                    ]
                ]
            ], true),
        ]);

        // Check group.
        $agent = $this->agents->getOne(['mbox' => 'mailto:promo@traxlrs.com']);
        $this->assertTrue($agent->name == 'Promo 2013-S1');
        $this->assertTrue(count($agent->members) == 2);

        // Check agent.
        $agent = $this->agents->getOne(['mbox' => 'mailto:learner1@traxlrs.com']);
        $this->assertTrue($agent->name == 'Learner 1');

        // Check event.
        if ($this->isLocalService()) {
            Event::assertDispatched(AgentsUpdated::class, 1);
        }
    }

    public function testUpdateFromStatementsApi()
    {
        $this->clearAgents();

        $this->recordStatementsBatch([
            Statements::simple([
                'actor' => [
                    'mbox' => 'mailto:agent@traxlrs.com',
                ],
            ], true),
        ]);

        if ($this->isRemoteService()) {
            sleep(1);
        }

        // Count agents. 2 because there is the authority.
        $this->assertTrue($this->agents->count() == 2);
    }
}
