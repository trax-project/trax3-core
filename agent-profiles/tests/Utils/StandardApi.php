<?php

namespace Trax\AgentProfiles\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository;

class StandardApi extends ServiceApi
{
    protected $service = 'agent-profiles';

    protected $profiles;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->profiles = app(AgentProfileRepository::class);
    }
}
