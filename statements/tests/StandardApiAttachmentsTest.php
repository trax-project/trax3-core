<?php

namespace Trax\Statements\Tests;

use Trax\Framework\Tests\Data\Statements;
use Trax\Statements\Tests\Utils\StandardApi;
use Trax\Statements\Tests\Utils\PostMultipart;
use Trax\Statements\Tests\Utils\RecordStatements;

class StandardApiAttachmentsTest extends StandardApi
{
    use RecordStatements, PostMultipart;
    
    public function testPostOne()
    {
        $this->clearStatements();

        $contentAndBoundary = Statements::embeddedAttachment([], false);

        $this->postMultipart(
            $this->storeApiEndpoint('xapi/statements'),
            $contentAndBoundary->content,
            $contentAndBoundary->boundary
        )->assertOk();

        $this->assertTrue($this->attachments->count() == 1);
    }
}
