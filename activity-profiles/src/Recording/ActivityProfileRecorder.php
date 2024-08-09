<?php

namespace Trax\ActivityProfiles\Recording;

use Trax\Framework\Database\DataRecorder;
use Trax\Framework\Service\Facades\EventManager;
use Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository;
use Trax\ActivityProfiles\Events\ActivityProfilesUpdated;

class ActivityProfileRecorder implements DataRecorder
{
    /**
     * @var \Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository
     */
    protected $profiles;
    
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->profiles = app(ActivityProfileRepository::class);
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
        EventManager::dispatch(ActivityProfilesUpdated::class, $records);

        return collect($records)->pluck('id')->all();
    }
}
