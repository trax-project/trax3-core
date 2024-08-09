<?php

namespace Trax\Agents\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\Agents\Repos\Agent\AgentRepository;

class StandardApi extends ServiceApi
{
    protected $service = 'agents';

    protected $agents;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->agents = app(AgentRepository::class);
    }
}
