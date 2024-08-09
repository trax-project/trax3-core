<?php

namespace Trax\AgentProfiles\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\Framework\Service\Config;

class ExtendedApi extends ServiceApi
{
    protected $service = 'agent-profiles';

    public function setUp(): void
    {
        parent::setUp();

        if (!Config::extendedEdition()) {
            $this->markTestSkipped('Extended Edition disabled');
        }
    }
}
