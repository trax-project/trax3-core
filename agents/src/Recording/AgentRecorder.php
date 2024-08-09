<?php

namespace Trax\Agents\Recording;

use Trax\Framework\Database\DataRecorder;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Agents\Repos\Agent\AgentRepository;
use Trax\Agents\Events\AgentsUpdated;

class AgentRecorder implements DataRecorder
{
    /**
     * @var \Trax\Agents\Repos\Agent\AgentRepository
     */
    protected $agents;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->agents = app(AgentRepository::class);
    }
    
    /**
     * Record agents.
     *
     * @param  array  $records
     * @return array
     */
    public function record(array $records): array
    {
        // Update the agents.
        $records = collect(
            $this->agents->upsert($records)
        );

        // Dispatch events.
        if (!$records->isEmpty()) {
            EventManager::dispatch(AgentsUpdated::class, $records);
        }

        return $records->pluck('id')->all();
    }
}
