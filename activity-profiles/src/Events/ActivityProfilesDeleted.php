<?php
 
namespace Trax\ActivityProfiles\Events;
 
use Trax\Framework\Service\Event\Event;
 
class ActivityProfilesDeleted extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'activity-profiles-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'activity-profiles-deleted';
    }
}
