<?php

namespace Trax\AgentProfiles;

use Trax\Framework\Database\Database;
use Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository;
use Trax\AgentProfiles\Recording\AgentProfileRecorder;

class AgentProfilesDatabase extends Database
{
    /**
     * @var string
     */
    protected $service = 'agent-profiles';
                    
    /**
     * @var string
     */
    protected static $connection = 'agent-profiles';
    
    /**
     * @var array
     */
    protected static $tables = [
        'xapi_agent_profiles' => ['type' => 'main'],
    ];

    /**
     * @var array
     */
    protected $storeTables = ['xapi_agent_profiles'];

    /**
     * @var bool
     */
    protected $nosqlSupported = true;

    /**
     * @var \Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository
     */
    protected $xapi_agent_profiles;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->xapi_agent_profiles = app(AgentProfileRepository::class);
        $this->mainRepository = $this->xapi_agent_profiles;
        $this->recorder = app(AgentProfileRecorder::class);
    }

    /**
     * Clear users data.
     *
     * @param  array  $users    each listed user is an agent string ID
     * @return void
     */
    public function clearUsers(array $users): void
    {
        $this->xapi_agent_profiles->clearAgents($users);
    }
}
