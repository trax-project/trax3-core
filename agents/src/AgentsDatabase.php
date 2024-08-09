<?php

namespace Trax\Agents;

use Trax\Framework\Database\Database;
use Trax\Agents\Repos\Agent\AgentRepository;
use Trax\Agents\Recording\AgentRecorder;

class AgentsDatabase extends Database
{
    /**
     * @var string
     */
    protected $service = 'agents';
                
    /**
     * @var string
     */
    protected static $connection = 'agents';
    
    /**
     * @var array
     */
    protected static $tables = [
        'xapi_agents' => ['type' => 'main'],
    ];

    /**
     * @var array
     */
    protected $storeTables = ['xapi_agents'];

    /**
     * @var bool
     */
    protected $nosqlSupported = true;

    /**
     * @var \Trax\Agents\Repos\Agent\AgentRepository
     */
    protected $xapi_agents;
    
    /**
     * @return void
     */
    public function __construct()
    {
        $this->xapi_agents = app(AgentRepository::class);
        $this->mainRepository = $this->xapi_agents;
        $this->recorder = app(AgentRecorder::class);
    }

    /**
     * Clear users data.
     *
     * @param  array  $users    each listed user is an agent string ID
     * @return void
     */
    public function clearUsers(array $users): void
    {
        $this->xapi_agents->clearAgents($users);
    }
}
