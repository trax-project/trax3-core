<?php

namespace Trax\Activities\Recording;

use Trax\Framework\Database\DataRecorder;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Activities\Repos\Activity\ActivityRepository;
use Trax\Activities\Events\ActivitiesUpdated;

class ActivityRecorder implements DataRecorder
{
    /**
     * @var \Trax\Activities\Repos\Activity\ActivityRepository
     */
    protected $activities;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->activities = app(ActivityRepository::class);
    }
    
    /**
     * Record activities.
     *
     * @param  array  $records
     * @return array
     */
    public function record(array $records): array
    {
        // Update the activities.
        $records = collect(
            $this->activities->upsert($records)
        );

        // Dispatch events.
        if (!$records->isEmpty()) {
            EventManager::dispatch(ActivitiesUpdated::class, $records);
        }

        return $records->pluck('id')->all();
    }
}
