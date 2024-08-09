<?php

namespace Trax\Statements\Tests;

use Trax\Framework\Tests\Data\Statements;
use Trax\Statements\Tests\Utils\StandardApi;
use Trax\Statements\Tests\Utils\RecordStatements;

class StandardApiVoidingTest extends StandardApi
{
    use RecordStatements;

    public function testVoiding()
    {
        $uuid1 = traxUuid();
        $uuid2 = traxUuid();

        // We create a statement to be voided.
        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid1]),
            Statements::simple()
        )->assertStatus(204);

        // We void the first statement.
        $this->putJson(
            $this->storeApiEndpoint('xapi/statements', ['statementId' => $uuid2]),
            Statements::voided($uuid1)
        )->assertStatus(204);

        // We check the voided statement.
        $voided = $this->statements->find($uuid1);
        $this->assertTrue($voided !== null);
        $this->assertTrue($voided->voided);

        // We check the voiding statement.
        $voiding = $this->statements->find($uuid2);
        $this->assertTrue($voiding !== null);
        $this->assertTrue($voiding->voiding);

        // Now we try to void the voiding statement.
        $this->postJson(
            $this->storeApiEndpoint('xapi/statements'),
            Statements::voided($uuid2)
        )->assertStatus(400);
    }
}
