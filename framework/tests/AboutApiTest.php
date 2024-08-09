<?php

namespace Trax\Framework\Tests;

use Trax\Statements\Tests\Utils\ExtendedApi;

class AboutApiTest extends ExtendedApi
{
    public function testGetAbout()
    {
        $this->getJson(
            $this->storeApiEndpoint('xapi/about')
        )->assertOk();
    }
}
