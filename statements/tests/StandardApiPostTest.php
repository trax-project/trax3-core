<?php

namespace Trax\Statements\Tests;

use Trax\Framework\Tests\Data\Statements;
use Trax\Statements\Tests\Utils\StandardApi;
use Trax\Statements\Tests\Utils\RecordStatements;

class StandardApiPostTest extends StandardApi
{
    use RecordStatements;

    public function testPostSingleWithoutId()
    {
        $this->clearStatements();

        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            Statements::simple()
        )->assertOk()->assertJsonCount(1);
        
        $this->assertTrue(
            $this->statements->count() == 1
        );
    }

    public function testPostSingleWithId()
    {
        $uuid = traxUuid();

        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            Statements::simple(['id' => $uuid])
        )->assertOk()->assertJsonCount(1);

        $this->assertTrue($this->statements->find($uuid) !== null);
    }

    public function testPostBatch()
    {
        $this->clearStatements();

        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            [Statements::simple(), Statements::simple()]
        )->assertOk()->assertJsonCount(2);

        $this->assertTrue(
            $this->statements->count() == 2
        );
    }

    public function testPostEmptyBatch()
    {
        $this->clearStatements();

        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            []
        )->assertOk()->assertJsonCount(0);

        $this->assertTrue(
            $this->statements->count() == 0
        );
    }

    public function testPostInvalidStatement()
    {
        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            [Statements::simple(['actor' => 'invalid'])]
        )->assertStatus(400);
    }

    public function testPostInvalidParam()
    {
        $this->postJson(
            $this->storeApiEndpoint('xapi/statements', ['invalid' => 'param']),
            Statements::simple()
        )->assertStatus(400);
    }

    public function testWrongRequests()
    {
        $this->delete(
            $this->storeApiEndpoint('xapi/statements')
        )->assertStatus(403);
    }
}
