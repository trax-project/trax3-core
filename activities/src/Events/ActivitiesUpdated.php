<?php
 
namespace Trax\Activities\Events;
 
use Trax\Framework\Service\Event\Event;
 
class ActivitiesUpdated extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'activities-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'activities-updated';
    }
}
