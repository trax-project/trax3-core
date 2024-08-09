<?php

namespace Trax\States\Recording;

use Trax\Framework\Database\DataRecorder;
use Trax\Framework\Service\Facades\EventManager;
use Trax\States\Repos\State\StateRepository;
use Trax\States\Events\StatesUpdated;

class StateRecorder implements DataRecorder
{
    /**
     * @var \Trax\States\Repos\State\StateRepository
     */
    protected $states;
    
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->states = app(StateRepository::class);
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
        $records = $this->states->upsert($records);

        // Events.
        EventManager::dispatch(StatesUpdated::class, $records);

        return collect($records)->pluck('id')->all();
    }
}
