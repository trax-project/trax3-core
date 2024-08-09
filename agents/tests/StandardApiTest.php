<?php

namespace Trax\Agents\Tests;

use Trax\Agents\Tests\Utils\StandardApi;
use Trax\Agents\Tests\Utils\RecordAgents;

class StandardApiTest extends StandardApi
{
    use RecordAgents;

    public function testGetNonExisting()
    {
        $person = $this->getJson(
            $this->storeApiEndpoint('xapi/agents', ['agent' => $this->john(true)])
        )->assertOk()->json();

        $this->assertTrue(isset($person['account']) && count($person['account']) == 1);
        $this->assertTrue($person['account'][0]['name'] == 'john');
    }

    public function testGetExisting()
    {
        $this->createAgent($this->john());

        $person = $this->getJson(
            $this->storeApiEndpoint('xapi/agents', ['agent' => $this->john(true)])
        )->assertOk()->json();

        $this->assertTrue(isset($person['account']) && count($person['account']) == 1);
        $this->assertTrue($person['account'][0]['name'] == 'john');
    }

    public function testAlternateRequest()
    {
        $person = $this->post(
            $this->storeApiEndpoint('xapi/agents', ['method' => 'get']),
            [
                'agent' => $this->john(true)
            ]
        )->assertOk()->json();

        $this->assertTrue(isset($person['account']) && count($person['account']) == 1);
        $this->assertTrue($person['account'][0]['name'] == 'john');
    }

    public function testWrongRequests()
    {
        // Missing agent.
        $this->getJson(
            $this->storeApiEndpoint('xapi/agents')
        )->assertStatus(400);

        // Invalid param.
        $this->getJson(
            $this->storeApiEndpoint('xapi/agents', [
                'agent' => $this->john(true),
                'invalid' => 'param',
            ])
        )->assertStatus(400);

        // Unsupported method.
        $this->putJson(
            $this->storeApiEndpoint('xapi/agents', [
                'agent' => $this->john(true),
            ])
        )->assertStatus(403);
    }
}
