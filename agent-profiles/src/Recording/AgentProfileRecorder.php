<?php

namespace Trax\AgentProfiles\Recording;

use Trax\Framework\Database\DataRecorder;
use Trax\Framework\Service\Facades\EventManager;
use Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository;
use Trax\AgentProfiles\Events\AgentProfilesUpdated;

class AgentProfileRecorder implements DataRecorder
{
    /**
     * @var \Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository
     */
    protected $profiles;
    
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->profiles = app(AgentProfileRepository::class);
    }
    
    /**
     * Record states.
     *
     * @param  array  $records
     * @return array
     */
    public function record(array $records): array
    {
        // Update the states.
        $records = $this->profiles->upsert($records);

        // Events.
        EventManager::dispatch(AgentProfilesUpdated::class, $records);

        return collect($records)->pluck('id')->all();
    }
}
