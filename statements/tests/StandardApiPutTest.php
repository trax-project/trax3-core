<?php

namespace Trax\Statements\Tests;

use Trax\Framework\Tests\Data\Statements;
use Trax\Framework\Tests\Utils\RawRequests;
use Trax\Statements\Tests\Utils\StandardApi;

class StandardApiPutTest extends StandardApi
{
    use RawRequests;

    public function testPutWithoutId()
    {
        $uuid = traxUuid();

        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid]),
            Statements::simple()
        )->assertStatus(204);

        $this->assertTrue($this->statements->find($uuid) !== null);
    }

    public function testPutIncludingId()
    {
        $uuid = traxUuid();

        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid]),
            Statements::simple(['id' => $uuid])
        )->assertStatus(204);

        $this->assertTrue($this->statements->find($uuid) !== null);
    }

    public function testPutWithAlternateRequest()
    {
        $uuid = traxUuid();

        $this->post(
            $this->storeApiEndpoint('xapi/statements', ['method' => 'put']),
            [
                'content' => json_encode(Statements::simple()),
                'statementId' => $uuid,
            ]
        )->assertStatus(204);

        $this->assertTrue($this->statements->find($uuid) !== null);
    }

    public function testPutWithInvalidAlternateRequest()
    {
        $uuid = traxUuid();

        $this->post(
            $this->storeApiEndpoint('xapi/statements', ['method' => 'put']),
            []
        )->assertStatus(400);
    }

    public function testPutWithWrongAlternateRequest()
    {
        $uuid = traxUuid();

        $this->post(
            $this->storeApiEndpoint('xapi/statements', ['method' => 'put', 'statementId' => $uuid]),
            [
                'content' => json_encode(Statements::simple()),
                'statementId' => $uuid,
            ]
        )->assertStatus(400);
    }

    public function testPutInvalidId()
    {
        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => traxUuid()]),
            Statements::simple(['id' => traxUuid()])
        )->assertStatus(400);
    }

    public function testPutInvalidStatement()
    {
        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => traxUuid()]),
            Statements::simple(['actor' => 'invalid'])
        )->assertStatus(400);
    }

    public function testPutInvalidParam()
    {
        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['invalid' => 'param']),
            Statements::simple()
        )->assertStatus(400);
    }

    public function testPutMissingParam()
    {
        $this->putJson(
            $this->storeApiEndpoint('xapi/statements'),
            Statements::simple()
        )->assertStatus(400);
    }

    public function testPutIdConflict()
    {
        $uuid = traxUuid();

        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid]),
            Statements::simple()
        )->assertStatus(204);

        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid]),
            Statements::simple()
        )->assertStatus(409);
    }
}
